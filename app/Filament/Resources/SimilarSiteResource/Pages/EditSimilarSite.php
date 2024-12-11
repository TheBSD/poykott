<?php

namespace App\Filament\Resources\SimilarSiteResource\Pages;

use App\Filament\Resources\SimilarSiteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSimilarSite extends EditRecord
{
    protected static string $resource = SimilarSiteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
