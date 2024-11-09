<?php

namespace App\Filament\Resources\PersonResourcesResource\Pages;

use App\Filament\Resources\PersonResourcesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPersonResources extends ListRecords
{
    protected static string $resource = PersonResourcesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
