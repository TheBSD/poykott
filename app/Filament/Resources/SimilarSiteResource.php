<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SimilarSiteResource\Pages\CreateSimilarSite;
use App\Filament\Resources\SimilarSiteResource\Pages\EditSimilarSite;
use App\Filament\Resources\SimilarSiteResource\Pages\ListSimilarSites;
use App\Models\SimilarSite;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
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
                    ->required(),
                TextInput::make('url')
                    ->unique()
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                //Forms\Components\Select::make('parent_id')
                //    ->relationship(
                //        name: 'parent',
                //        titleAttribute: 'name',
                //        modifyQueryUsing: fn (Builder $query) => $query->whereNull('parent_id'),
                //    ),

                Select::make('similar_site_category_id')
                    ->relationship('similarSiteCategory', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('url')
                    ->searchable(),

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
            //
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
