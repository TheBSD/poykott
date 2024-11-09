<?php

namespace App\Filament\Resources\AlternativeResource\Pages;

use App\Filament\Resources\AlternativeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlternative extends EditRecord
{
    protected static string $resource = AlternativeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
