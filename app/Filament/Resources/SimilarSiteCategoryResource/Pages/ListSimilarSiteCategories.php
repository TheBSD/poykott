<?php

namespace App\Filament\Resources\SimilarSiteCategoryResource\Pages;

use App\Filament\Resources\SimilarSiteCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSimilarSiteCategories extends ListRecords
{
    protected static string $resource = SimilarSiteCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
