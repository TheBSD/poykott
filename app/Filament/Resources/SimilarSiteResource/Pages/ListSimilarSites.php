<?php

namespace App\Filament\Resources\SimilarSiteResource\Pages;

use App\Filament\Resources\SimilarSiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSimilarSites extends ListRecords
{
    protected static string $resource = SimilarSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
