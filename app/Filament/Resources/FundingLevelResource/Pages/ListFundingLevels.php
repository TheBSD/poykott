<?php

namespace App\Filament\Resources\FundingLevelResource\Pages;

use App\Filament\Resources\FundingLevelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFundingLevels extends ListRecords
{
    protected static string $resource = FundingLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
