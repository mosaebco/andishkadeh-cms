<?php

namespace App\Filament\Resources\ContentItems;

use App\Filament\Resources\ContentItems\Pages\CreateContentItem;
use App\Filament\Resources\ContentItems\Pages\EditContentItem;
use App\Filament\Resources\ContentItems\Pages\ListContentItems;
use App\Models\ContentItem;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class ContentItemResource extends Resource
{
    protected static ?string $model = ContentItem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Content';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Content details')
                ->columns(2)
                ->schema([
                    Select::make('type')
                        ->label('Content type')
                        ->options([
                            'post' => 'Post',
                            'course' => 'Course',
                            'book' => 'Book',
                            'announcement' => 'Announcement',
                        ])
                        ->default('post')
                        ->live()
                        ->required(),
                    Select::make('series_id')
                        ->label('Series (posts only)')
                        ->relationship('series', 'title')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->visible(fn (Get $get): bool => $get('type') === 'post'),
                    TextInput::make('title')->required()->maxLength(180),
                    TextInput::make('slug')
                        ->nullable()
                        ->alphaDash()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('type', $get('type')),
                        )
                        ->maxLength(200)
                        ->helperText('Leave blank to generate from the title.'),
                    Textarea::make('excerpt')->label('Short description')->rows(3)->maxLength(500)->columnSpanFull(),
                    FileUpload::make('cover_image_path')
                        ->label('Banner / cover image')
                        ->image()
                        ->imageEditor()
                        ->disk(config('media.disk'))
                        ->directory('andishkadeh/content/covers')
                        ->visibility('public')
                        ->maxSize(config('media.max_size_kb.image'))
                        ->preventFilePathTampering()
                        ->required(fn (Get $get): bool => in_array($get('type'), ['course', 'book'], true))
                        ->columnSpanFull(),
                    TextInput::make('sort_order')->numeric()->default(0)->minValue(0),
                ]),
            Section::make('Content')
                ->description('The public page renders text, then videos/images, then audio, then links.')
                ->schema([
                    Builder::make('content_blocks')
                        ->label('Content blocks')
                        ->blocks([
                            Block::make('rich_text')->label('Text')->icon('heroicon-o-bars-3-bottom-left')->schema([
                                RichEditor::make('body')->label('')->required(),
                            ]),
                            Block::make('heading')->icon('heroicon-o-hashtag')->schema([
                                TextInput::make('text')->required()->maxLength(200),
                                Select::make('level')->options(['2' => 'Heading 2', '3' => 'Heading 3', '4' => 'Heading 4'])->default('2')->required(),
                            ])->columns(2),
                            Block::make('image')->icon('heroicon-o-photo')->schema([
                                FileUpload::make('path')->image()->imageEditor()->disk(config('media.disk'))->directory('andishkadeh/content/images')->visibility('public')->maxSize(config('media.max_size_kb.image'))->preventFilePathTampering()->required(),
                                TextInput::make('alt')->label('Alternative text')->maxLength(220),
                                TextInput::make('caption')->maxLength(220),
                            ]),
                            Block::make('gallery')->icon('heroicon-o-squares-2x2')->schema([
                                FileUpload::make('paths')->label('Images')->multiple()->reorderable()->image()->disk(config('media.disk'))->directory('andishkadeh/content/galleries')->visibility('public')->maxSize(config('media.max_size_kb.image'))->preventFilePathTampering()->required(),
                            ]),
                            Block::make('video')->icon('heroicon-o-video-camera')->schema([
                                FileUpload::make('path')->label('Local video')->disk(config('media.disk'))->directory('andishkadeh/content/videos')->visibility('public')->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime', 'video/x-msvideo'])->maxSize(config('media.max_size_kb.video'))->preventFilePathTampering(),
                                TextInput::make('url')
                                    ->label('Or external video URL')
                                    ->url()
                                    ->maxLength(2048)
                                    ->visible((bool) config('media.allow_external_urls')),
                                TextInput::make('caption')->maxLength(220),
                            ]),
                            Block::make('audio')->icon('heroicon-o-speaker-wave')->schema([
                                FileUpload::make('path')->label('Audio file')->disk(config('media.disk'))->directory('andishkadeh/content/audio')->visibility('public')->acceptedFileTypes(['audio/mpeg', 'audio/mp4', 'audio/x-m4a', 'audio/aac', 'audio/ogg', 'audio/wav', 'audio/flac'])->maxSize(config('media.max_size_kb.audio'))->preventFilePathTampering()->required(),
                                TextInput::make('caption')->maxLength(220),
                            ]),
                            Block::make('quote')->icon('heroicon-o-chat-bubble-left-right')->schema([
                                Textarea::make('text')->required()->rows(4),
                                TextInput::make('citation')->maxLength(180),
                            ]),
                            Block::make('download')->icon('heroicon-o-arrow-down-tray')->schema([
                                FileUpload::make('path')->label('File')->disk(config('media.disk'))->directory('andishkadeh/content/files')->visibility('public')->maxSize(config('media.max_size_kb.download'))->preventFilePathTampering()->required(),
                                TextInput::make('label')->required()->maxLength(160),
                            ]),
                        ])
                        ->collapsible()
                        ->reorderable()
                        ->blockNumbers()
                        ->addActionLabel('Add content block'),
                ]),
            Section::make('Related links')
                ->schema([
                    Repeater::make('links')
                        ->relationship()
                        ->orderColumn('sort_order')
                        ->schema([
                            TextInput::make('label')->required()->maxLength(160),
                            TextInput::make('url')->url()->required()->maxLength(2048),
                            Textarea::make('description')->rows(2)->maxLength(320)->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel('Add labeled link'),
                ]),
            Section::make('Publishing')
                ->columns(2)
                ->schema([
                    Select::make('status')->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])->default('draft')->required(),
                    DateTimePicker::make('published_at')->label('Publish at')->seconds(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('cover_image_path')->label('')->disk(config('media.disk'))->square(),
                TextColumn::make('title')->searchable()->weight('bold'),
                TextColumn::make('type')->badge()->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('published_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')->options([
                    'post' => 'Post',
                    'course' => 'Course',
                    'book' => 'Book',
                    'announcement' => 'Announcement',
                ]),
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'scheduled' => 'Scheduled',
                    'published' => 'Published',
                    'archived' => 'Archived',
                ]),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContentItems::route('/'),
            'create' => CreateContentItem::route('/create'),
            'edit' => EditContentItem::route('/{record}/edit'),
        ];
    }
}
