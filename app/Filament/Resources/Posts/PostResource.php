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

    protected static ?string $modelLabel = 'مطلب';

    protected static ?string $pluralModelLabel = 'مطالب';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('جزئیات مطلب')
                ->columns(2)
                ->schema([
                    Select::make('series_id')->label('مجموعه (اختیاری)')->relationship('series', 'title')->searchable()->preload()->nullable(),
                    TextInput::make('sort_order')->label('ترتیب در مجموعه')->numeric()->default(0)->minValue(0),
                    TextInput::make('title')->label('عنوان')->required()->maxLength(180),
                    TextInput::make('slug')
                        ->nullable()
                        ->alphaDash()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule): Unique => $rule->where('type', 'post'),
                        )
                        ->maxLength(200)
                        ->helperText('برای ساخت خودکار از روی عنوان، خالی بگذارید.'),
                    Textarea::make('excerpt')->label('توضیح کوتاه')->rows(3)->maxLength(500)->columnSpanFull(),
                    FileUpload::make('cover_image_path')
                        ->label('تصویر روی جلد')
                        ->image()
                        ->imageEditor()
                        ->disk(config('media.disk'))
                        ->directory('andishkadeh/posts/covers')
                        ->visibility('public')
                        ->maxSize(config('media.max_size_kb.image'))
                        ->preventFilePathTampering()
                        ->columnSpanFull(),
                ]),
            Section::make('محتوا')
                ->description('بلوک‌ها به‌ترتیب متن، ویدیوها و تصاویر، فایل‌های صوتی و سپس پیوندها نمایش داده می‌شوند. در هر بخش می‌توان چند مورد اضافه کرد.')
                ->schema([
                    Builder::make('content_blocks')
                        ->label('بلوک‌های مطلب')
                        ->blocks([
                            Block::make('rich_text')->label('متن')->icon('heroicon-o-bars-3-bottom-left')->schema([
                                RichEditor::make('body')->label('')->required(),
                            ]),
                            Block::make('heading')->label('عنوان میان‌متنی')->icon('heroicon-o-hashtag')->schema([
                                TextInput::make('text')->label('متن عنوان')->required()->maxLength(200),
                                Select::make('level')->label('سطح عنوان')->options(['2' => 'عنوان ۲', '3' => 'عنوان ۳', '4' => 'عنوان ۴'])->default('2')->required(),
                            ])->columns(2),
                            Block::make('image')->label('تصویر')->icon('heroicon-o-photo')->schema([
                                FileUpload::make('path')->label('تصویر')->image()->imageEditor()->disk(config('media.disk'))->directory('andishkadeh/posts/images')->visibility('public')->maxSize(config('media.max_size_kb.image'))->preventFilePathTampering()->required(),
                                TextInput::make('alt')->label('متن جایگزین')->maxLength(220),
                                TextInput::make('caption')->label('زیرنویس')->maxLength(220),
                            ]),
                            Block::make('gallery')->label('گالری تصاویر')->icon('heroicon-o-squares-2x2')->schema([
                                FileUpload::make('paths')->label('تصاویر')->multiple()->reorderable()->image()->disk(config('media.disk'))->directory('andishkadeh/posts/galleries')->visibility('public')->maxSize(config('media.max_size_kb.image'))->preventFilePathTampering()->required(),
                            ]),
                            Block::make('audio')->label('فایل صوتی')->icon('heroicon-o-speaker-wave')->schema([
                                FileUpload::make('path')->label('فایل صوتی')->disk(config('media.disk'))->directory('andishkadeh/posts/audio')->visibility('public')->acceptedFileTypes(['audio/mpeg', 'audio/mp4', 'audio/x-m4a', 'audio/aac', 'audio/ogg', 'audio/wav', 'audio/flac'])->maxSize(config('media.max_size_kb.audio'))->preventFilePathTampering()->required(),
                                TextInput::make('caption')->label('عنوان صوت')->maxLength(220),
                            ]),
                            Block::make('video')->label('ویدیو')->icon('heroicon-o-video-camera')->schema([
                                FileUpload::make('path')->label('ویدیوی محلی')->disk(config('media.disk'))->directory('andishkadeh/posts/video')->visibility('public')->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime', 'video/x-msvideo'])->maxSize(config('media.max_size_kb.video'))->preventFilePathTampering(),
                                TextInput::make('url')
                                    ->label('یا نشانی ویدیوی خارجی')
                                    ->url()
                                    ->maxLength(2048)
                                    ->visible((bool) config('media.allow_external_urls')),
                                TextInput::make('caption')->label('زیرنویس')->maxLength(220),
                            ]),
                            Block::make('link')->label('پیوند')->icon('heroicon-o-link')->schema([
                                TextInput::make('label')->label('برچسب پیوند')->required()->maxLength(160),
                                TextInput::make('url')->label('نشانی پیوند')->url()->required()->maxLength(2048),
                                Textarea::make('description')->label('توضیح پیوند')->rows(2)->maxLength(320)->columnSpanFull(),
                            ])->columns(2),
                            Block::make('quote')->label('نقل‌قول')->icon('heroicon-o-chat-bubble-left-right')->schema([
                                Textarea::make('text')->label('متن نقل‌قول')->required()->rows(4),
                                TextInput::make('citation')->label('منبع')->maxLength(180),
                            ]),
                            Block::make('download')->label('فایل دانلودی')->icon('heroicon-o-arrow-down-tray')->schema([
                                FileUpload::make('path')->label('فایل')->disk(config('media.disk'))->directory('andishkadeh/posts/files')->visibility('public')->maxSize(config('media.max_size_kb.download'))->preventFilePathTampering()->required(),
                                TextInput::make('label')->label('عنوان فایل')->required()->maxLength(160),
                            ]),
                        ])
                        ->collapsible()
                        ->reorderable()
                        ->blockNumbers()
                        ->addActionLabel('افزودن بلوک محتوا'),
                ]),
            Section::make('انتشار')
                ->columns(2)
                ->schema([
                    Select::make('status')->label('وضعیت')->options([
                        'draft' => 'پیش‌نویس',
                        'published' => 'منتشرشده',
                        'archived' => 'بایگانی‌شده',
                    ])->default('draft')->required(),
                    DateTimePicker::make('published_at')->label('زمان انتشار')->jalali()->displayFormat('Y/m/d H:i')->seconds(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('cover_image_path')->label('')->disk(config('media.disk'))->square(),
                TextColumn::make('title')->label('عنوان')->searchable()->weight('bold'),
                TextColumn::make('series.title')->label('مجموعه')->searchable()->sortable()->badge(),
                TextColumn::make('sort_order')->label('ترتیب')->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge()->sortable()->formatStateUsing(fn (string $state): string => match ($state) {
                    'draft' => 'پیش‌نویس',
                    'published' => 'منتشرشده',
                    'archived' => 'بایگانی‌شده',
                    default => $state,
                }),
                TextColumn::make('published_at')->label('زمان انتشار')->jalaliDateTime('Y/m/d H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('series')->label('مجموعه')->relationship('series', 'title'),
                SelectFilter::make('status')->label('وضعیت')->options([
                    'draft' => 'پیش‌نویس', 'published' => 'منتشرشده', 'archived' => 'بایگانی‌شده',
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
