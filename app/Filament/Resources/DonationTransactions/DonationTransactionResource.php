<?php

namespace App\Filament\Resources\DonationTransactions;

use App\Filament\Resources\DonationTransactions\Pages\ListDonationTransactions;
use App\Models\DonationTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DonationTransactionResource extends Resource
{
    protected static ?string $model = DonationTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'تراکنش‌های اهدا';

    protected static ?string $modelLabel = 'تراکنش اهدا';

    protected static ?string $pluralModelLabel = 'تراکنش‌های اهدا';

    protected static ?int $navigationSort = 12;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('amount_toman')->label('مبلغ (تومان)')->numeric()->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge()->sortable()->formatStateUsing(fn (string $state): string => match ($state) {
                    'pending' => 'در انتظار',
                    'paid' => 'پرداخت‌شده',
                    'failed' => 'ناموفق',
                    'expired' => 'منقضی‌شده',
                    default => $state,
                }),
                TextColumn::make('gateway_reference')->label('مرجع درگاه')->searchable(),
                TextColumn::make('created_at')->label('زمان ایجاد')->dateTime()->sortable(),
                TextColumn::make('verified_at')->label('زمان تأیید')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('وضعیت')->options([
                    'pending' => 'در انتظار',
                    'paid' => 'پرداخت‌شده',
                    'failed' => 'ناموفق',
                    'expired' => 'منقضی‌شده',
                ]),
            ])
            ->recordActions([]);
    }

    public static function getPages(): array
    {
        return ['index' => ListDonationTransactions::route('/')];
    }
}
