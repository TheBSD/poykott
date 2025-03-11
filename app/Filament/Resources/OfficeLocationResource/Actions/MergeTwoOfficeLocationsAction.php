<?php

namespace App\Filament\Resources\OfficeLocationResource\Actions;

use App\Actions\OfficeLocationsMergerAction;
use App\Models\OfficeLocation;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class MergeTwoOfficeLocationsAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->label('Merge with')
            ->icon('heroicon-o-arrows-pointing-in')
            ->color('gray')
            ->form(function (OfficeLocation $record): array {
                return [
                    Select::make('name')
                        ->label("Office Location to merge with ({$record->name})")
                        ->searchable()
                        ->required()
                        ->options(
                            OfficeLocation::query()->where('id', '!=', $record->id)  // Avoid selecting the current location
                                ->pluck('name', 'id')
                        ),
                ];
            })
            ->action(function (OfficeLocation $record, array $data): void {

                $to = $record;
                $from = OfficeLocation::query()->find((int) $data['name']);

                $action = resolve(OfficeLocationsMergerAction::class);

                $action->execute($from, $to);

                Notification::make()
                    ->success()
                    ->title('Office locations merged')
                    ->body("({$from->name}) merged to ({$to->name})")
                    ->send();
            });
    }
}
