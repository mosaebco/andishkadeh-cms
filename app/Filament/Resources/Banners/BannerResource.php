<?php

namespace App\Filament\Resources\Banners;

use App\Filament\Resources\Banners\Pages\ManageBanners;
use App\Models\Banner;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'بنرهای صفحه اصلی';

    protected static ?string $modelLabel = 'بنر';

    protected static ?string $pluralModelLabel = 'بنرهای صفحه اصلی';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('محتوای بنر')
                ->columns(2)
                ->schema([
                    TextInput::make('title')->label('عنوان')->required()->maxLength(160),
                    TextInput::make('link_label')->label('متن دکمه')->maxLength(80),
                    Textarea::make('subtitle')->label('توضیح کوتاه')->rows(3)->maxLength(320)->columnSpanFull(),
                    FileUpload::make('image_path')
                        ->label('تصویر بنر')
                        ->image()
                        ->imageEditor()
                        ->disk(config('media.disk'))
                        ->directory('andishkadeh/banners')
                        ->visibility('public')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])
                        ->maxSize(config('media.max_size_kb.image'))
                        ->preventFilePathTampering()
                        ->required()
                        ->columnSpanFull(),
                    TextInput::make('link_url')->label('نشانی دکمه')->url()->maxLength(2048)->columnSpanFull(),
                ]),
            Section::make('نمایش')
                ->columns(4)
                ->schema([
                    Toggle::make('is_active')->label('فعال')->default(false),
                    TextInput::make('sort_order')->label('ترتیب نمایش')->numeric()->default(0)->minValue(0),
                    DateTimePicker::make('starts_at')->label('شروع نمایش')->jalali()->displayFormat('Y/m/d H:i')->seconds(false),
                    DateTimePicker::make('ends_at')->label('پایان نمایش')->jalali()->displayFormat('Y/m/d H:i')->seconds(false)->after('starts_at'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('image_path')->label('')->disk(config('media.disk'))->square(),
                TextColumn::make('title')->label('عنوان')->searchable()->weight('bold'),
                TextColumn::make('sort_order')->label('ترتیب')->sortable(),
                IconColumn::make('is_active')->boolean()->label('فعال'),
                TextColumn::make('starts_at')->label('شروع نمایش')->jalaliDateTime('Y/m/d H:i')->sortable()->toggleable(),
                TextColumn::make('ends_at')->label('پایان نمایش')->jalaliDateTime('Y/m/d H:i')->sortable()->toggleable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageBanners::route('/')];
    }
}
