<?php

namespace App\Filament\Resources\ExitStrategyResource\Pages;

use App\Filament\Resources\ExitStrategyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExitStrategies extends ListRecords
{
    protected static string $resource = ExitStrategyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
