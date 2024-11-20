<?php

namespace App\Filament\Resources\PersonResource\Pages;

use App\Filament\Resources\PersonResource;
use Filament\Resources\Pages\EditRecord;

class EditPerson extends EditRecord
{
    protected static string $resource = PersonResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['social_links'])) {
            $data['social_links'] = array_map('trim', explode(',', $data['social_links']));
        }

        return $data;
    }
}
