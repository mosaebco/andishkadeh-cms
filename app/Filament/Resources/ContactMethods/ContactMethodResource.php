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

    protected static ?string $navigationLabel = 'راه‌های ارتباطی';

    protected static ?string $modelLabel = 'راه ارتباطی';

    protected static ?string $pluralModelLabel = 'راه‌های ارتباطی';

    protected static ?int $navigationSort = 11;

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('اطلاعات راه ارتباطی')
                ->columns(2)
                ->schema([
                    TextInput::make('label')->label('عنوان')->required()->maxLength(120),
                    Select::make('type')->label('نوع')->options([
                        'phone' => 'تلفن ثابت',
                        'mobile' => 'تلفن همراه',
                        'email' => 'ایمیل',
                        'address' => 'نشانی',
                        'map' => 'پیوند نقشه',
                        'telegram' => 'تلگرام',
                        'instagram' => 'اینستاگرام',
                        'whatsapp' => 'واتساپ',
                        'eitaa' => 'ایتا',
                        'link' => 'پیوند دیگر',
                    ])->required(),
                    TextInput::make('value')->label('مقدار یا نشانی')->required()->maxLength(2048)->columnSpanFull(),
                    TextInput::make('icon')->label('آیکن')->maxLength(80),
                    TextInput::make('sort_order')->label('ترتیب نمایش')->numeric()->default(0)->minValue(0),
                    Toggle::make('is_visible')->label('قابل نمایش')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('label')->label('عنوان')->searchable()->weight('bold'),
                TextColumn::make('type')->label('نوع')->badge()->formatStateUsing(fn (string $state): string => match ($state) {
                    'phone' => 'تلفن ثابت',
                    'mobile' => 'تلفن همراه',
                    'email' => 'ایمیل',
                    'address' => 'نشانی',
                    'map' => 'پیوند نقشه',
                    'telegram' => 'تلگرام',
                    'instagram' => 'اینستاگرام',
                    'whatsapp' => 'واتساپ',
                    'eitaa' => 'ایتا',
                    'link' => 'پیوند دیگر',
                    default => $state,
                }),
                TextColumn::make('value')->label('مقدار یا نشانی')->limit(40),
                TextColumn::make('sort_order')->label('ترتیب')->sortable(),
                IconColumn::make('is_visible')->boolean()->label('قابل نمایش'),
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
