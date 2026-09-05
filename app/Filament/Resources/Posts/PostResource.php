<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Models\Post;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    /**
     * Keep the prototype routes available for backwards compatibility while
     * directing administrators to the unified Content resource.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Post details')
                ->columns(2)
                ->schema([
                    Select::make('series_id')->label('Series (optional)')->relationship('series', 'title')->searchable()->preload()->nullable(),
                    TextInput::make('sort_order')->label('Order in series')->numeric()->default(0)->minValue(0),
                    TextInput::make('title')->required()->maxLength(180),
                    TextInput::make('slug')
                        ->nullable()
                        ->alphaDash()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule): Unique => $rule->where('type', 'post'),
                        )
                        ->maxLength(200)
                        ->helperText('Leave blank to generate from the title.'),
                    Textarea::make('excerpt')->rows(3)->maxLength(500)->columnSpanFull(),
                    FileUpload::make('cover_image_path')
                        ->label('Cover image')
                        ->image()
                        ->imageEditor()
                        ->disk(config('media.disk'))
                        ->directory('andishkadeh/posts/covers')
                        ->visibility('public')
                        ->maxSize(config('media.max_size_kb.image'))
                        ->preventFilePathTampering()
                        ->columnSpanFull(),
                ]),
            Section::make('Content')
                ->description('Blocks render as text, videos/images, audio, then links. Multiple items are supported within each section.')
                ->schema([
                    Builder::make('content_blocks')
                        ->label('Post blocks')
                        ->blocks([
                            Block::make('rich_text')->label('Text')->icon('heroicon-o-bars-3-bottom-left')->schema([
                                RichEditor::make('body')->label('')->required(),
                            ]),
                            Block::make('heading')->icon('heroicon-o-hashtag')->schema([
                                TextInput::make('text')->required()->maxLength(200),
                                Select::make('level')->options(['2' => 'Heading 2', '3' => 'Heading 3', '4' => 'Heading 4'])->default('2')->required(),
                            ])->columns(2),
                            Block::make('image')->icon('heroicon-o-photo')->schema([
                                FileUpload::make('path')->image()->imageEditor()->disk(config('media.disk'))->directory('andishkadeh/posts/images')->visibility('public')->maxSize(config('media.max_size_kb.image'))->preventFilePathTampering()->required(),
                                TextInput::make('alt')->label('Alternative text')->maxLength(220),
                                TextInput::make('caption')->maxLength(220),
                            ]),
                            Block::make('gallery')->icon('heroicon-o-squares-2x2')->schema([
                                FileUpload::make('paths')->label('Images')->multiple()->reorderable()->image()->disk(config('media.disk'))->directory('andishkadeh/posts/galleries')->visibility('public')->maxSize(config('media.max_size_kb.image'))->preventFilePathTampering()->required(),
                            ]),
                            Block::make('audio')->icon('heroicon-o-speaker-wave')->schema([
                                FileUpload::make('path')->label('Audio file')->disk(config('media.disk'))->directory('andishkadeh/posts/audio')->visibility('public')->acceptedFileTypes(['audio/mpeg', 'audio/mp4', 'audio/x-m4a', 'audio/aac', 'audio/ogg', 'audio/wav', 'audio/flac'])->maxSize(config('media.max_size_kb.audio'))->preventFilePathTampering()->required(),
                                TextInput::make('caption')->maxLength(220),
                            ]),
                            Block::make('video')->icon('heroicon-o-video-camera')->schema([
                                FileUpload::make('path')->label('Local video')->disk(config('media.disk'))->directory('andishkadeh/posts/video')->visibility('public')->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime', 'video/x-msvideo'])->maxSize(config('media.max_size_kb.video'))->preventFilePathTampering(),
                                TextInput::make('url')
                                    ->label('Or external video URL')
                                    ->url()
                                    ->maxLength(2048)
                                    ->visible((bool) config('media.allow_external_urls')),
                                TextInput::make('caption')->maxLength(220),
                            ]),
                            Block::make('link')->icon('heroicon-o-link')->schema([
                                TextInput::make('label')->required()->maxLength(160),
                                TextInput::make('url')->url()->required()->maxLength(2048),
                                Textarea::make('description')->rows(2)->maxLength(320)->columnSpanFull(),
                            ])->columns(2),
                            Block::make('quote')->icon('heroicon-o-chat-bubble-left-right')->schema([
                                Textarea::make('text')->required()->rows(4),
                                TextInput::make('citation')->maxLength(180),
                            ]),
                            Block::make('download')->icon('heroicon-o-arrow-down-tray')->schema([
                                FileUpload::make('path')->label('File')->disk(config('media.disk'))->directory('andishkadeh/posts/files')->visibility('public')->maxSize(config('media.max_size_kb.download'))->preventFilePathTampering()->required(),
                                TextInput::make('label')->required()->maxLength(160),
                            ]),
                        ])
                        ->collapsible()
                        ->reorderable()
                        ->blockNumbers()
                        ->addActionLabel('Add content block'),
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
                TextColumn::make('series.title')->searchable()->sortable()->badge(),
                TextColumn::make('sort_order')->label('Order')->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('published_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('series')->relationship('series', 'title'),
                SelectFilter::make('status')->options([
                    'draft' => 'Draft', 'scheduled' => 'Scheduled', 'published' => 'Published', 'archived' => 'Archived',
                ]),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
