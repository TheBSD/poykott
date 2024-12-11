<?php

namespace App\Filament\Resources\SimilarSiteResource\Pages;

use App\Filament\Resources\SimilarSiteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSimilarSite extends CreateRecord
{
    protected static string $resource = SimilarSiteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
