<?php

namespace App\Filament\Resources\SimilarSiteCategoryResource\Pages;

use App\Filament\Resources\SimilarSiteCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSimilarSiteCategory extends EditRecord
{
    protected static string $resource = SimilarSiteCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
