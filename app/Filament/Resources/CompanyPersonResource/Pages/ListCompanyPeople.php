<?php

namespace App\Filament\Resources\CompanyPersonResource\Pages;

use App\Filament\Resources\CompanyPersonResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyPeople extends ListRecords
{
    protected static string $resource = CompanyPersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
