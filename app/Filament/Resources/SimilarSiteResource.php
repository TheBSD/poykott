<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SimilarSiteResource\Pages;
use App\Models\SimilarSite;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SimilarSiteResource extends Resource
{
    protected static ?string $model = SimilarSite::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-top-right-on-square';

    protected static ?string $navigationGroup = 'Similar Sites';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('url'),
                Forms\Components\Textarea::make('description')
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable(),

                TextColumn::make('similarSiteCategory.name')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSimilarSites::route('/'),
            'create' => Pages\CreateSimilarSite::route('/create'),
            'edit' => Pages\EditSimilarSite::route('/{record}/edit'),
        ];
    }
}
