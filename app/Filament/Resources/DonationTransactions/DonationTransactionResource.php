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

    protected static ?string $navigationLabel = 'Donations';

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
                TextColumn::make('amount_toman')->label('Amount (toman)')->numeric()->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('gateway_reference')->label('Gateway reference')->searchable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('verified_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'failed' => 'Failed',
                    'expired' => 'Expired',
                ]),
            ])
            ->recordActions([]);
    }

    public static function getPages(): array
    {
        return ['index' => ListDonationTransactions::route('/')];
    }
}
