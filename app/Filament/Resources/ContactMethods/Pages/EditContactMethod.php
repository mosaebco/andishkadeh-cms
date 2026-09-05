<?php

namespace App\Filament\Resources\ContactMethods\Pages;

use App\Filament\Resources\ContactMethods\ContactMethodResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContactMethod extends EditRecord
{
    protected static string $resource = ContactMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
