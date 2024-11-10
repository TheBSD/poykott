<?php

namespace App\Filament\Resources\PersonResourcesResource\Pages;

use App\Filament\Resources\PersonResourcesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonResources extends EditRecord
{
    protected static string $resource = PersonResourcesResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
