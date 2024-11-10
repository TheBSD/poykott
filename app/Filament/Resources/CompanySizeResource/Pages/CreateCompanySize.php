<?php

namespace App\Filament\Resources\CompanySizeResource\Pages;

use App\Filament\Resources\CompanySizeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCompanySize extends CreateRecord
{
    protected static string $resource = CompanySizeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
