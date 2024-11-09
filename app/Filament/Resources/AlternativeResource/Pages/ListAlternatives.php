<?php

namespace App\Filament\Resources\AlternativeResource\Pages;

use App\Filament\Resources\AlternativeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlternatives extends ListRecords
{
    protected static string $resource = AlternativeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
