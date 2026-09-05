<?php

namespace App\Filament\Resources\ContactMethods;

use App\Filament\Resources\ContactMethods\Pages\CreateContactMethod;
use App\Filament\Resources\ContactMethods\Pages\EditContactMethod;
use App\Filament\Resources\ContactMethods\Pages\ListContactMethods;
use App\Models\ContactMethod;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactMethodResource extends Resource
{
    protected static ?string $model = ContactMethod::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-phone';

    protected static ?string $navigationLabel = 'Contact methods';

    protected static ?int $navigationSort = 11;

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contact method')
                ->columns(2)
                ->schema([
                    TextInput::make('label')->required()->maxLength(120),
                    Select::make('type')->options([
                        'phone' => 'Landline',
                        'mobile' => 'Mobile',
                        'email' => 'Email',
                        'address' => 'Address',
                        'map' => 'Map link',
                        'telegram' => 'Telegram',
                        'instagram' => 'Instagram',
                        'whatsapp' => 'WhatsApp',
                        'eitaa' => 'Eitaa',
                        'link' => 'Other link',
                    ])->required(),
                    TextInput::make('value')->label('Value or URL')->required()->maxLength(2048)->columnSpanFull(),
                    TextInput::make('icon')->maxLength(80),
                    TextInput::make('sort_order')->numeric()->default(0)->minValue(0),
                    Toggle::make('is_visible')->label('Visible')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('label')->searchable()->weight('bold'),
                TextColumn::make('type')->badge(),
                TextColumn::make('value')->limit(40),
                TextColumn::make('sort_order')->sortable(),
                IconColumn::make('is_visible')->boolean()->label('Visible'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMethods::route('/'),
            'create' => CreateContactMethod::route('/create'),
            'edit' => EditContactMethod::route('/{record}/edit'),
        ];
    }
}
