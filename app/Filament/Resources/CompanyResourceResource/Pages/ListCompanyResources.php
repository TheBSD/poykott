<?php

namespace App\Filament\Resources\CompanyResourceResource\Pages;

use App\Filament\Resources\CompanyResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyResources extends ListRecords
{
    protected static string $resource = CompanyResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
