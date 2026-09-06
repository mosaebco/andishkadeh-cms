<?php

namespace App\Filament\Resources\Series;

use App\Filament\Resources\Series\Pages\CreateSeries;
use App\Filament\Resources\Series\Pages\EditSeries;
use App\Filament\Resources\Series\Pages\ListSeries;
use App\Models\Series;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
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

class SeriesResource extends Resource
{
    protected static ?string $model = Series::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'مجموعه‌ها';

    protected static ?string $modelLabel = 'مجموعه';

    protected static ?string $pluralModelLabel = 'مجموعه‌ها';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('جزئیات مجموعه')
                ->columns(2)
                ->schema([
                    TextInput::make('title')->label('عنوان')->required()->maxLength(180),
                    TextInput::make('slug')->label('نامک')->nullable()->alphaDash()->unique(ignoreRecord: true)->maxLength(200)->helperText('برای ساخت خودکار از روی عنوان، خالی بگذارید.'),
                    Textarea::make('description')->label('توضیحات')->rows(5)->maxLength(1200)->columnSpanFull(),
                    FileUpload::make('cover_image_path')
                        ->label('تصویر روی جلد')
                        ->image()
                        ->imageEditor()
                        ->disk(config('media.disk'))
                        ->directory('andishkadeh/series')
                        ->visibility('public')
                        ->maxSize(config('media.max_size_kb.image'))
                        ->preventFilePathTampering(),
                    TextInput::make('sort_order')->label('ترتیب نمایش')->numeric()->default(0)->minValue(0),
                ]),
            Section::make('انتشار')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->label('وضعیت')
                        ->options([
                            'draft' => 'پیش‌نویس',
                            'published' => 'منتشرشده',
                            'archived' => 'بایگانی‌شده',
                        ])
                        ->default('draft')
                        ->required(),
                    DateTimePicker::make('published_at')
                        ->label('زمان انتشار')
                        ->jalali()
                        ->displayFormat('Y/m/d H:i')
                        ->seconds(false)
                        ->helperText('موارد منتشرشده فقط پس از این زمان نمایش داده می‌شوند.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('cover_image_path')->label('')->disk(config('media.disk'))->square(),
                TextColumn::make('title')->label('عنوان')->searchable()->weight('bold'),
                TextColumn::make('posts_count')->counts('posts')->label('مطالب'),
                TextColumn::make('status')->label('وضعیت')->badge()->sortable()->formatStateUsing(fn (string $state): string => match ($state) {
                    'draft' => 'پیش‌نویس',
                    'published' => 'منتشرشده',
                    'archived' => 'بایگانی‌شده',
                    default => $state,
                }),
                TextColumn::make('published_at')->label('زمان انتشار')->jalaliDateTime('Y/m/d H:i')->sortable(),
            ])
            ->filters([SelectFilter::make('status')->label('وضعیت')->options([
                'draft' => 'پیش‌نویس',
                'published' => 'منتشرشده',
                'archived' => 'بایگانی‌شده',
            ])])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeries::route('/'),
            'create' => CreateSeries::route('/create'),
            'edit' => EditSeries::route('/{record}/edit'),
        ];
    }
}
