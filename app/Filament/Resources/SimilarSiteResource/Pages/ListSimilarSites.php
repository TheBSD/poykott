<?php

namespace App\Filament\Resources\SimilarSiteResource\Pages;

use App\Filament\Resources\SimilarSiteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSimilarSites extends ListRecords
{
    protected static string $resource = SimilarSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
