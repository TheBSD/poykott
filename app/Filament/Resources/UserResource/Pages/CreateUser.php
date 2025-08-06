<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        /**
         * All of this to prevent silently discarding attributes
         */
        $role = $data['roles'] ?? null;
        unset($data['roles']);

        $user = static::getModel()::create($data);

        if ($role) {
            $user->roles()->sync([$role]);
        }

        return $user;
    }
}
