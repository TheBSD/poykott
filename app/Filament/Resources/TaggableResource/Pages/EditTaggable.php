<?php

namespace App\Filament\Resources\TaggableResource\Pages;

use App\Filament\Resources\TaggableResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaggable extends EditRecord
{
    protected static string $resource = TaggableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
