<?php

namespace App\Filament\Resources\TagResource\Actions;

use App\Actions\TagsMergerAction;
use App\Models\Tag;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class MergeTwoTagsAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->label('Merge with')
            ->icon('heroicon-o-arrows-pointing-in')
            ->color('gray')
            ->form(function (Tag $record): array {
                return [
                    Select::make('name')
                        ->label("Tag to merge with ({$record->name})")
                        ->searchable()
                        ->required()
                        ->options(
                            Tag::query()->where('id', '!=', $record->id)  // Avoid selecting the current location
                                ->pluck('name', 'id')
                        ),
                ];
            })
            ->action(function (Tag $record, array $data): void {

                $to = $record;

                $from = Tag::query()->find((int) $data['name']);

                $action = resolve(TagsMergerAction::class);

                $action->execute($from, $to);

                Notification::make()
                    ->success()
                    ->title('Tags merged')
                    ->body("({$from->name}) merged to ({$to->name})")
                    ->send();
            });
    }
}
