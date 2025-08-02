<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditResource\Pages\ListAudits;
use App\Filament\Resources\AuditResource\Pages\ViewAudit;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;
use OwenIt\Auditing\Contracts\Audit;

class AuditResource extends Resource
{
    protected static ?string $model = \OwenIt\Auditing\Models\Audit::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationGroup = 'Internals';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('user_id')
                                    ->label('User'),

                                TextInput::make('event')
                                    ->formatStateUsing(
                                        fn ($record) => Str::title($record->event)
                                    ),

                                TextInput::make('auditable_type')
                                    ->label('Model')
                                    ->formatStateUsing(
                                        fn ($record) => Str::title(class_basename($record->auditable_type))
                                    ),

                                TextInput::make('auditable_id')
                                    ->label('Model ID'),

                                TextInput::make('ip_address')
                                    ->label('IP Address'),

                                TextInput::make('created_at')
                                    ->formatStateUsing(
                                        fn ($record
                                        ): string => $record->created_at->format('Y-m-d H:i:s') . '  [' . $record->created_at->diffForHumans() . ']'
                                    ),
                            ])
                            ->columns(),
                    ]),

                Section::make('Changes')
                    ->schema([
                        Section::make()
                            ->schema([
                                Textarea::make('old_values')
                                    ->label('Old Values')
                                    ->formatStateUsing(fn ($record) => $record->old_values
                                        ?
                                        json_encode(
                                            $record->old_values,
                                            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                                        )
                                        : null)
                                    ->rows(10)
                                    ->columnSpanFull(),

                                Textarea::make('new_values')
                                    ->label('New Values')
                                    ->formatStateUsing(fn ($record) => $record->new_values
                                        ?
                                        json_encode(
                                            $record->new_values,
                                            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                                        )
                                        : null)
                                    ->rows(10)
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Metadata')
                    ->schema([
                        TextInput::make('url')
                            ->label('URL')
                            ->columnSpanFull(),

                        TextInput::make('user_agent')
                            ->label('User Agent')
                            ->columnSpanFull(),

                        TextInput::make('tags')
                            ->label('Tags')
                            ->default(fn ($record) => is_array($record->tags)
                                ? implode(', ', $record->tags)
                                : ($record->tags ?? 'N/A'))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Audit ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user_type')
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
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('auditable_id')
                    ->label('Model')
                    ->url(fn (\OwenIt\Auditing\Models\Audit $record) => $record->auditable_type && $record->auditable_id
                        ? route('filament.admin.resources.' . Str::plural(Str::lower(class_basename($record->auditable_type))) . '.edit',
                            ['record' => $record->auditable_id])
                        : null)
                    ->formatStateUsing(
                        fn ($record): string => Str::title(class_basename($record->auditable_type)) . ':' . $record->auditable_id
                    )
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('old_values')
                    ->label('Old Values')
                    ->formatStateUsing(fn (
                        Column $column,
                        $record,
                        $state
                    ): View => view('vendor.filament-auditing.tables.columns.key-value', [
                        'state' => $column->getState(),
                    ])),

                TextColumn::make('new_values')
                    ->label('New Values')
                    ->formatStateUsing(fn (
                        Column $column,
                        $record,
                        $state
                    ): View => view('vendor.filament-auditing.tables.columns.key-value', [
                        'state' => $column->getState(),
                    ])),
                TextColumn::make('url')
                    ->limit(30)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                SelectFilter::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
            ])
            ->actions([
                Action::make('restore')
                    ->label('Restore Updated')
                    ->action(fn (Audit $record) => static::restoreAudit($record))
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => $record->event === 'updated'),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->deferLoading()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAudits::route('/'),
            'view' => ViewAudit::route('/{record}'),
        ];
    }

    /**
     * This resource is read-only.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    protected static function notifyRestored(): void
    {
        Notification::make()
            ->title('Audit restored')
            ->success()
            ->send();
    }

    protected static function notifyUnchanged(): void
    {
        Notification::make()
            ->title('Nothing to change')
            ->warning()
            ->send();
    }

    private static function restoreAudit(Audit $audit): void
    {
        $morphClass = Relation::getMorphedModel($audit->auditable_type) ?? $audit->auditable_type;

        $record = $morphClass::find($audit->auditable_id);

        if (! $record) {
            self::notifyRestored();

            return;
        }

        if ($audit->event !== 'updated') {
            self::notifyUnchanged();

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

            self::notifyRestored();

            return;
        }

        self::notifyUnchanged();
    }
}
