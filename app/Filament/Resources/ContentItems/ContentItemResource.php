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

    protected static ?string $navigationLabel = 'محتوا';

    protected static ?string $modelLabel = 'محتوا';

    protected static ?string $pluralModelLabel = 'محتوا';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('جزئیات محتوا')
                ->columns(2)
                ->schema([
                    Select::make('type')
                        ->label('نوع محتوا')
                        ->options([
                            'post' => 'مطلب',
                            'course' => 'دوره',
                            'book' => 'کتاب',
                            'announcement' => 'اطلاعیه',
                        ])
                        ->default('post')
                        ->live()
                        ->required(),
                    Select::make('series_id')
                        ->label('مجموعه (فقط مطالب)')
                        ->relationship('series', 'title')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->visible(fn (Get $get): bool => $get('type') === 'post'),
                    TextInput::make('title')->label('عنوان')->required()->maxLength(180),
                    TextInput::make('slug')
                        ->label('نامک')
                        ->nullable()
                        ->alphaDash()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('type', $get('type')),
                        )
                        ->maxLength(200)
                        ->helperText('برای ساخت خودکار از روی عنوان، خالی بگذارید.'),
                    Textarea::make('excerpt')->label('توضیح کوتاه')->rows(3)->maxLength(500)->columnSpanFull(),
                    FileUpload::make('cover_image_path')
                        ->label('بنر / تصویر روی جلد')
                        ->image()
                        ->imageEditor()
                        ->disk(config('media.disk'))
                        ->directory('andishkadeh/content/covers')
                        ->visibility('public')
                        ->maxSize(config('media.max_size_kb.image'))
                        ->preventFilePathTampering()
                        ->required(fn (Get $get): bool => in_array($get('type'), ['course', 'book'], true))
                        ->columnSpanFull(),
                    TextInput::make('sort_order')->label('ترتیب نمایش')->numeric()->default(0)->minValue(0),
                ]),
            Section::make('محتوا')
                ->description('در صفحه عمومی، ابتدا متن، سپس ویدیوها و تصاویر، بعد فایل‌های صوتی و در پایان پیوندها نمایش داده می‌شوند.')
                ->schema([
                    Builder::make('content_blocks')
                        ->label('بلوک‌های محتوا')
                        ->blocks([
                            Block::make('rich_text')->label('متن')->icon('heroicon-o-bars-3-bottom-left')->schema([
                                RichEditor::make('body')->label('')->required(),
                            ]),
                            Block::make('heading')->label('عنوان میان‌متنی')->icon('heroicon-o-hashtag')->schema([
                                TextInput::make('text')->label('متن عنوان')->required()->maxLength(200),
                                Select::make('level')->label('سطح عنوان')->options(['2' => 'عنوان ۲', '3' => 'عنوان ۳', '4' => 'عنوان ۴'])->default('2')->required(),
                            ])->columns(2),
                            Block::make('image')->label('تصویر')->icon('heroicon-o-photo')->schema([
                                FileUpload::make('path')->label('تصویر')->image()->imageEditor()->disk(config('media.disk'))->directory('andishkadeh/content/images')->visibility('public')->maxSize(config('media.max_size_kb.image'))->preventFilePathTampering()->required(),
                                TextInput::make('alt')->label('متن جایگزین')->maxLength(220),
                                TextInput::make('caption')->label('زیرنویس')->maxLength(220),
                            ]),
                            Block::make('gallery')->label('گالری تصاویر')->icon('heroicon-o-squares-2x2')->schema([
                                FileUpload::make('paths')->label('تصاویر')->multiple()->reorderable()->image()->disk(config('media.disk'))->directory('andishkadeh/content/galleries')->visibility('public')->maxSize(config('media.max_size_kb.image'))->preventFilePathTampering()->required(),
                            ]),
                            Block::make('video')->label('ویدیو')->icon('heroicon-o-video-camera')->schema([
                                FileUpload::make('path')->label('ویدیوی محلی')->disk(config('media.disk'))->directory('andishkadeh/content/videos')->visibility('public')->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime', 'video/x-msvideo'])->maxSize(config('media.max_size_kb.video'))->preventFilePathTampering(),
                                TextInput::make('url')
                                    ->label('یا نشانی ویدیوی خارجی')
                                    ->url()
                                    ->maxLength(2048)
                                    ->visible((bool) config('media.allow_external_urls')),
                                TextInput::make('caption')->label('زیرنویس')->maxLength(220),
                            ]),
                            Block::make('audio')->label('فایل صوتی')->icon('heroicon-o-speaker-wave')->schema([
                                FileUpload::make('path')->label('فایل صوتی')->disk(config('media.disk'))->directory('andishkadeh/content/audio')->visibility('public')->acceptedFileTypes(['audio/mpeg', 'audio/mp4', 'audio/x-m4a', 'audio/aac', 'audio/ogg', 'audio/wav', 'audio/flac'])->maxSize(config('media.max_size_kb.audio'))->preventFilePathTampering()->required(),
                                TextInput::make('caption')->label('عنوان صوت')->maxLength(220),
                            ]),
                            Block::make('quote')->label('نقل‌قول')->icon('heroicon-o-chat-bubble-left-right')->schema([
                                Textarea::make('text')->label('متن نقل‌قول')->required()->rows(4),
                                TextInput::make('citation')->label('منبع')->maxLength(180),
                            ]),
                            Block::make('download')->label('فایل دانلودی')->icon('heroicon-o-arrow-down-tray')->schema([
                                FileUpload::make('path')->label('فایل')->disk(config('media.disk'))->directory('andishkadeh/content/files')->visibility('public')->maxSize(config('media.max_size_kb.download'))->preventFilePathTampering()->required(),
                                TextInput::make('label')->label('عنوان فایل')->required()->maxLength(160),
                            ]),
                        ])
                        ->collapsible()
                        ->reorderable()
                        ->blockNumbers()
                        ->addActionLabel('افزودن بلوک محتوا'),
                ]),
            Section::make('پیوندهای مرتبط')
                ->schema([
                    Repeater::make('links')
                        ->relationship()
                        ->orderColumn('sort_order')
                        ->schema([
                            TextInput::make('label')->label('برچسب پیوند')->required()->maxLength(160),
                            TextInput::make('url')->label('نشانی پیوند')->url()->required()->maxLength(2048),
                            Textarea::make('description')->label('توضیح پیوند')->rows(2)->maxLength(320)->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel('افزودن پیوند برچسب‌دار'),
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
                TextColumn::make('type')->label('نوع')->badge()->sortable()->formatStateUsing(fn (string $state): string => match ($state) {
                    'post' => 'مطلب',
                    'course' => 'دوره',
                    'book' => 'کتاب',
                    'announcement' => 'اطلاعیه',
                    default => $state,
                }),
                TextColumn::make('status')->label('وضعیت')->badge()->sortable()->formatStateUsing(fn (string $state): string => match ($state) {
                    'draft' => 'پیش‌نویس',
                    'published' => 'منتشرشده',
                    'archived' => 'بایگانی‌شده',
                    default => $state,
                }),
                TextColumn::make('published_at')->label('زمان انتشار')->jalaliDateTime('Y/m/d H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')->label('نوع محتوا')->options([
                    'post' => 'مطلب',
                    'course' => 'دوره',
                    'book' => 'کتاب',
                    'announcement' => 'اطلاعیه',
                ]),
                SelectFilter::make('status')->label('وضعیت')->options([
                    'draft' => 'پیش‌نویس',
                    'published' => 'منتشرشده',
                    'archived' => 'بایگانی‌شده',
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
