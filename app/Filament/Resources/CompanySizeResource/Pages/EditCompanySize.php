<?php

namespace App\Filament\Resources\CompanySizeResource\Pages;

use App\Filament\Resources\CompanySizeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanySize extends EditRecord
{
    protected static string $resource = CompanySizeResource::class;


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
