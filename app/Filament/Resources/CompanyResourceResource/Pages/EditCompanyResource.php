<?php

namespace App\Filament\Resources\CompanyResourceResource\Pages;

use App\Filament\Resources\CompanyResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyResource extends EditRecord
{
    protected static string $resource = CompanyResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
