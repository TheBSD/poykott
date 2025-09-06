<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessageResource\Pages\ViewContactMessage;
use App\Models\ContactMessage;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Contacts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Message')
                    ->schema([
                        TextInput::make('name'),
                        TextInput::make('email')->email(),
                        Textarea::make('message')->rows(8)->columnSpanFull(),
                    ]),

                Section::make('Message Info')
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Received at')
                            ->content(fn (?Model $record) => $record?->created_at?->format('F j, Y \a\t g:i A')),
                        Placeholder::make('read_at')
                            ->label('Read at')
                            ->content(fn (?Model $record) => $record?->read_at ? $record->read_at->format('F j, Y \a\t g:i A') : 'Not read yet'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->limit(20)->sortable(),
                TextColumn::make('email')->searchable()->copyable()->weight(FontWeight::Bold)->copyMessage('Email copied!')->sortable(),
                TextColumn::make('message')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= 30) {
                            return null;
                        }

                        return Str::limit($state, 250);
                    }),
                IconColumn::make('is_read')
                    ->label('Is Opened')
                    ->boolean()
                    ->tooltip(fn ($state): string => $state ? 'Message has been opened' : 'Message not opened yet')
                    ->sortable(),
                IconColumn::make('is_spam')
                    ->label('Spam Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-exclamation')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->tooltip(fn ($state): string => $state ? 'Marked as spam - email and IP blocked' : 'Not spam - legitimate message')
                    ->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_read')
                    ->label('Read Status')
                    ->placeholder('All messages')
                    ->trueLabel('Opened messages')
                    ->falseLabel('Not opened messages')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('read_at'),
                        false: fn (Builder $query) => $query->whereNull('read_at'),
                    ),

                TernaryFilter::make('spam_at')
                    ->label('Spam Status')
                    ->placeholder('All messages')
                    ->trueLabel('Spam messages')
                    ->falseLabel('Not spam messages')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('spam_at'),
                        false: fn (Builder $query) => $query->whereNull('spam_at'),
                    ),

                SelectFilter::make('created_at')
                    ->label('Received')
                    ->options([
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'this_week' => 'This week',
                        'this_month' => 'This month',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (! isset($data['value']) || ! $data['value']) {
                            return $query;
                        }

                        return match ($data['value']) {
                            'today' => $query->whereDate('created_at', today()),
                            'yesterday' => $query->whereDate('created_at', today()->subDay()),
                            'this_week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                            'this_month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),

                    Action::make('mark_as_read')
                        ->label('Mark as Opened')
                        ->icon('heroicon-m-check')
                        ->color('success')
                        ->visible(fn (Model $record): bool => ! $record->read_at)
                        ->action(fn (Model $record) => $record->update(['read_at' => now()]))
                        ->after(fn () => redirect()->back()),

                    Action::make('mark_as_unread')
                        ->label('Mark as Unopened')
                        ->icon('heroicon-m-x-mark')
                        ->color('warning')
                        ->visible(fn (Model $record) => $record->read_at)
                        ->action(fn (Model $record) => $record->update(['read_at' => null]))
                        ->after(fn () => redirect()->back()),

                    Action::make('mark_as_spam')
                        ->label('Mark as Spam')
                        ->icon('heroicon-m-shield-exclamation')
                        ->color('danger')
                        ->visible(fn (Model $record): bool => ! $record->spam_at)
                        ->requiresConfirmation()
                        ->modalHeading('Mark as Spam')
                        ->modalDescription('This will mark the message as spam and prevent future messages from this email address and IP address.')
                        ->action(fn (Model $record) => $record->markAsSpam())
                        ->after(fn () => redirect()->back()),

                    Action::make('mark_as_not_spam')
                        ->label('Mark as Not Spam')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->visible(fn (Model $record) => $record->spam_at)
                        ->requiresConfirmation()
                        ->modalHeading('Mark as Not Spam')
                        ->modalDescription('This will unmark the message as spam and allow future messages from this email address and IP address.')
                        ->action(fn (Model $record) => $record->markAsNotSpam())
                        ->after(fn () => redirect()->back()),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])

            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereNull('read_at')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'view' => ViewContactMessage::route('/{record}'),
        ];
    }
}
