<?php

namespace App\Filament\Resources\SiteSettings;

use App\Filament\Resources\SiteSettings\Pages\CreateSiteSetting;
use App\Filament\Resources\SiteSettings\Pages\EditSiteSetting;
use App\Filament\Resources\SiteSettings\Pages\ListSiteSettings;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'بخش‌های سایت';

    protected static ?string $modelLabel = 'بخش سایت';

    protected static ?string $pluralModelLabel = 'بخش‌های سایت';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'key';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('محتوای بخش')
                ->schema([
                    Select::make('key')
                        ->label('بخش')
                        ->options([
                            'about' => 'درباره ما',
                            'registration' => 'ثبت‌نام مؤسسه',
                            'donation' => 'اهدا',
                        ])
                        ->required()
                        ->live()
                        ->unique(ignoreRecord: true),
                    TextInput::make('title')->label('عنوان')->maxLength(180),
                    RichEditor::make('body')->label('توضیحات')->columnSpanFull(),
                    TextInput::make('url')->label('نشانی خارجی')->url()->maxLength(2048),
                    KeyValue::make('settings')
                        ->label('تنظیمات تکمیلی')
                        ->keyLabel('نام')
                        ->valueLabel('مقدار')
                        ->columnSpanFull()
                        ->visible(fn (Get $get): bool => $get('key') !== 'donation')
                        ->dehydrated(fn (Get $get): bool => $get('key') !== 'donation'),
                    TextInput::make('settings.minimum_amount')
                        ->label('حداقل مبلغ اهدا (تومان)')
                        ->numeric()
                        ->integer()
                        ->minValue(1)
                        ->visible(fn (Get $get): bool => $get('key') === 'donation')
                        ->dehydrated(fn (Get $get): bool => $get('key') === 'donation'),
                    TagsInput::make('settings.quick_amounts')
                        ->label('مبالغ پیشنهادی اهدا (تومان)')
                        ->separator(',')
                        ->trim()
                        ->nestedRecursiveRules(['integer', 'min:1'])
                        ->helperText('یک یا چند مبلغ کامل به تومان را با ویرگول جدا کنید.')
                        ->visible(fn (Get $get): bool => $get('key') === 'donation')
                        ->dehydrated(fn (Get $get): bool => $get('key') === 'donation')
                        ->columnSpanFull(),
                    Toggle::make('is_active')->label('قابل نمایش')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->label('بخش')->badge()->sortable()->formatStateUsing(fn (string $state): string => match ($state) {
                    'about' => 'درباره ما',
                    'registration' => 'ثبت‌نام مؤسسه',
                    'donation' => 'اهدا',
                    default => $state,
                }),
                TextColumn::make('title')->label('عنوان')->searchable(),
                IconColumn::make('is_active')->boolean()->label('قابل نمایش'),
                TextColumn::make('updated_at')->label('آخرین تغییر')->jalaliDateTime('Y/m/d H:i')->sortable(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteSettings::route('/'),
            'create' => CreateSiteSetting::route('/create'),
            'edit' => EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
