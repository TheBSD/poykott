<?php

namespace App\Filament\Resources;

use App\Actions\ScrapeLogoFromUrlAction;
use App\Filament\Resources\AuditsRelationManagerResource\RelationManagers\AuditsRelationManager;
use App\Filament\Resources\SimilarSiteResource\Pages\CreateSimilarSite;
use App\Filament\Resources\SimilarSiteResource\Pages\EditSimilarSite;
use App\Filament\Resources\SimilarSiteResource\Pages\ListSimilarSites;
use App\Models\SimilarSite;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SimilarSiteResource extends Resource
{
    protected static ?string $model = SimilarSite::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-top-right-on-square';

    protected static ?string $navigationGroup = 'Similar Sites';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->rules([
                        'required', 'string', 'max:255', 'min:3',
                    ])->unique(column: 'name', ignoreRecord: true),
                TextInput::make('url')
                    ->rules(['required', 'url', 'active_url'])->unique(column: 'url', ignoreRecord: true),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->rule([
                        'image',
                        'mimes:jpeg,jpg,png,svg,webp',
                        'max:2048',  // 2MB limit
                    ]),
                Textarea::make('description')
                    ->rules(['nullable', 'string', 'max:255']),
                Select::make('similar_site_category_id')
                    ->relationship('similarSiteCategory', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')->size(70),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('url')
                    ->searchable()
                    ->html()
                    ->formatStateUsing(function ($record): string {
                        return "<a href='{$record->url}' target='_blank' class='underline'>{$record->url}</a>";
                    })
                    ->color('info')
                    ->disabledClick(),
                TextColumn::make('similarSiteCategory.name')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                Action::make('fetchLogo')
                    ->label('Fetch Logo')
                    ->action(function (SimilarSite $record): void {

                        $success = app(ScrapeLogoFromUrlAction::class)->execute($record, $record->url);

                        if (! $success) {
                            Notification::make()
                                ->danger()
                                ->title('Failed fetching logo. Try uploading the logo manually')
                                ->send();
                        } else {
                            Notification::make()
                                ->success()
                                ->title('Logo fetched')
                                ->send();
                        }
                    })
                    ->requiresConfirmation(function ($record): bool {
                        return $record->media->count() > 0;
                    })
                    ->color('gray'),

                Action::make('removeLogo')
                    ->label('Remove Logo')
                    ->action(function (SimilarSite $record): void {
                        $record->clearMediaCollection();
                        Notification::make()
                            ->success()
                            ->title('Logo removed')
                            ->send();
                    })
                    ->requiresConfirmation(function ($record): bool {
                        return $record->media->count() > 0;
                    })
                    ->visible(function ($record): bool {
                        return $record->media->count() > 0;
                    })
                    ->color('danger'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AuditsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSimilarSites::route('/'),
            'create' => CreateSimilarSite::route('/create'),
            'edit' => EditSimilarSite::route('/{record}/edit'),
        ];
    }
}
