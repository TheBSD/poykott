<?php

namespace App\Filament\Resources\AuditsRelationManagerResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use OwenIt\Auditing\Contracts\Audit;

class AuditsRelationManager extends RelationManager
{
    protected static string $relationship = 'audits';

    protected static ?string $recordTitleAttribute = 'id';

    protected static function restoreAuditSelected($audit): void
    {
        $morphClass = Relation::getMorphedModel($audit->auditable_type) ?? $audit->auditable_type;

        $record = $morphClass::find($audit->auditable_id);

        if (! $record) {
            self::unchangedAuditNotification();

            return;
        }

        if ($audit->event !== 'updated') {
            self::unchangedAuditNotification();

            return;
        }

        $restore = $audit->old_values;

        Arr::pull($restore, 'id');

        if (is_array($restore)) {

            foreach ($restore as $key => $item) {
                $decode = json_decode((string) $item);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $restore[$key] = $decode;
                }
            }

            $record->fill($restore);
            $record->save();

            self::restoredAuditNotification();

            return;
        }

        self::unchangedAuditNotification();
    }

    protected static function restoredAuditNotification(): void
    {
        Notification::make()
            ->title('Audit restored')
            ->success()
            ->send();
    }

    protected static function unchangedAuditNotification(): void
    {
        Notification::make()
            ->title('Nothing to change')
            ->warning()
            ->send();
    }

    protected function canCreate(): bool
    {
        return false;
    }

    protected function canEdit(Model $record): bool
    {
        return false;
    }

    protected function canDelete(Model $record): bool
    {
        return false;
    }

    protected function canDeleteAny(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('created_at', 'desc'))
            ->emptyStateHeading('No Audits')
            ->columns([
                TextColumn::make('id')
                    ->label('Audit ID')
                    ->sortable()
                    ->url(fn (\OwenIt\Auditing\Models\Audit $record) => $record->user_id
                        ? route('filament.admin.resources.audits.view', ['record' => $record->id])
                        : null)
                    ->searchable(),

                TextColumn::make('user_id')
                    ->label('User')
                    ->sortable()
                    ->url(fn (\OwenIt\Auditing\Models\Audit $record) => $record->user_id
                        ? route('filament.admin.resources.users.edit', ['record' => $record->user_id])
                        : null)
                    ->formatStateUsing(
                        fn ($record): string => 'user:' . $record->user_id
                    )
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('event')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->formatStateUsing(
                        fn ($record
                        ): string => $record->created_at->format('Y-m-d H:i:s') . '  [' . $record->created_at->diffForHumans() . ']'
                    )
                    ->toggleable(),

                TextColumn::make('old_values')
                    ->label('Old Values')
                    ->formatStateUsing(function (
                        Column $column,
                        $record,
                        $state
                    ): View {
                        return view('vendor.filament-auditing.tables.columns.key-value', [
                            'state' => $column->getState(),
                        ]);
                    }),

                TextColumn::make('new_values')
                    ->label('New Values')
                    ->formatStateUsing(fn (
                        Column $column,
                        $record,
                        $state
                    ): View => view('vendor.filament-auditing.tables.columns.key-value', [
                        'state' => $column->getState(),
                    ])),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->wrap()
                    ->limit(40)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return mb_strlen($state) > 40 ? $state : null;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tags')
                    ->wrap()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Action::make('restore')
                    ->label('Restore Updated')
                    ->action(fn (Audit $record) => static::restoreAuditSelected($record))
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => $record->event === 'updated'),
            ])
            ->bulkActions([
                //
            ]);
    }
}
