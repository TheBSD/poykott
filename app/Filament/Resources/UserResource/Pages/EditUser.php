<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /**
         * All of this to prevent silently discarding attributes
         */
        $role = $data['roles'] ?? null;
        unset($data['roles']);

        $record->update($data);

        if ($role) {
            $record->roles()->sync([$role]);
        }

        return $record;
    }
}
