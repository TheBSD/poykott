<?php

namespace App\Filament\Resources\CompanySizeResource\Pages;

use App\Filament\Resources\CompanySizeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanySizes extends ListRecords
{
    protected static string $resource = CompanySizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
