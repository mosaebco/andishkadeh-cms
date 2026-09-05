<?php

namespace App\Filament\Resources\ContactMethods\Pages;

use App\Filament\Resources\ContactMethods\ContactMethodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContactMethods extends ListRecords
{
    protected static string $resource = ContactMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('New contact method')];
    }
}
