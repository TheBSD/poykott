<?php

namespace App\Filament\Resources\ExitStrategyResource\Pages;

use App\Filament\Resources\ExitStrategyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExitStrategy extends EditRecord
{
    protected static string $resource = ExitStrategyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
