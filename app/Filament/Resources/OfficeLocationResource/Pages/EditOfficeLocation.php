<?php

namespace App\Filament\Resources\OfficeLocationResource\Pages;

use App\Filament\Resources\OfficeLocationResource;
use Filament\Resources\Pages\EditRecord;

class EditOfficeLocation extends EditRecord
{
    protected static string $resource = OfficeLocationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
