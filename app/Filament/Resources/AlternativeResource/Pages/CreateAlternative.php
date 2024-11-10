<?php

namespace App\Filament\Resources\AlternativeResource\Pages;

use App\Filament\Resources\AlternativeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAlternative extends CreateRecord
{
    protected static string $resource = AlternativeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
