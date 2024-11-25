<?php

namespace App\Filament\Resources\ExitStrategyResource\Pages;

use App\Filament\Resources\ExitStrategyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExitStrategies extends ListRecords
{
    protected static string $resource = ExitStrategyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
