<?php

namespace App\Filament\Resources;

use App\Actions\ScrapeLogoFromUrlAction;
use App\Filament\Resources\AuditsRelationManagerResource\RelationManagers\AuditsRelationManager;
use App\Filament\Resources\SimilarSiteResource\Pages\CreateSimilarSite;
use App\Filament\Resources\SimilarSiteResource\Pages\EditSimilarSite;
use App\Filament\Resources\SimilarSiteResource\Pages\ListSimilarSites;
use App\Models\SimilarSite;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;
use UnitEnum;

class SimilarSiteResource extends Resource
{
    protected static ?string $model = SimilarSite::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-top-right-on-square';

    protected static string|UnitEnum|null $navigationGroup = 'Similar Sites';

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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

    #[Override]
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
            ->recordActions([
                EditAction::make(),
                Action::make('fetchLogo')
                    ->label('Fetch Logo')
                    ->action(function (SimilarSite $record): void {

                        $success = resolve(ScrapeLogoFromUrlAction::class)->execute($record, $record->url);

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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            AuditsRelationManager::class,
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListSimilarSites::route('/'),
            'create' => CreateSimilarSite::route('/create'),
            'edit' => EditSimilarSite::route('/{record}/edit'),
        ];
    }
}
