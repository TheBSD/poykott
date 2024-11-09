<?php

namespace App\Filament\Resources\CompanyPersonResource\Pages;

use App\Filament\Resources\CompanyPersonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyPeople extends ListRecords
{
    protected static string $resource = CompanyPersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
