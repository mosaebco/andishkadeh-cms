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

    protected static ?string $navigationLabel = 'Site sections';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'key';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Section content')
                ->schema([
                    Select::make('key')
                        ->label('Section')
                        ->options([
                            'about' => 'About Us',
                            'registration' => 'Institute registration',
                            'donation' => 'Donation',
                        ])
                        ->required()
                        ->live()
                        ->unique(ignoreRecord: true),
                    TextInput::make('title')->maxLength(180),
                    RichEditor::make('body')->label('Description')->columnSpanFull(),
                    TextInput::make('url')->label('External URL')->url()->maxLength(2048),
                    KeyValue::make('settings')
                        ->label('Additional settings')
                        ->keyLabel('Name')
                        ->valueLabel('Value')
                        ->columnSpanFull()
                        ->visible(fn (Get $get): bool => $get('key') !== 'donation')
                        ->dehydrated(fn (Get $get): bool => $get('key') !== 'donation'),
                    TextInput::make('settings.minimum_amount')
                        ->label('Minimum donation (toman)')
                        ->numeric()
                        ->integer()
                        ->minValue(1)
                        ->visible(fn (Get $get): bool => $get('key') === 'donation')
                        ->dehydrated(fn (Get $get): bool => $get('key') === 'donation'),
                    TagsInput::make('settings.quick_amounts')
                        ->label('Quick donation amounts (toman)')
                        ->separator(',')
                        ->trim()
                        ->nestedRecursiveRules(['integer', 'min:1'])
                        ->helperText('Enter one or more whole-toman amounts, separated by commas.')
                        ->visible(fn (Get $get): bool => $get('key') === 'donation')
                        ->dehydrated(fn (Get $get): bool => $get('key') === 'donation')
                        ->columnSpanFull(),
                    Toggle::make('is_active')->label('Visible')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->badge()->sortable(),
                TextColumn::make('title')->searchable(),
                IconColumn::make('is_active')->boolean()->label('Visible'),
                TextColumn::make('updated_at')->dateTime()->sortable(),
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
