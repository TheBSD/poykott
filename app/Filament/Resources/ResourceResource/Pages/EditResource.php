<?php

namespace App\Filament\Resources\ResourceResource\Pages;

use App\Filament\Resources\ResourceResource;
use Filament\Resources\Pages\EditRecord;

class EditResource extends EditRecord
{
    protected static string $resource = ResourceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
