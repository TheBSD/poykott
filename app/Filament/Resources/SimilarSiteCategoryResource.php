<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SimilarSiteCategoryResource\Pages\CreateSimilarSiteCategory;
use App\Filament\Resources\SimilarSiteCategoryResource\Pages\EditSimilarSiteCategory;
use App\Filament\Resources\SimilarSiteCategoryResource\Pages\ListSimilarSiteCategories;
use App\Models\SimilarSiteCategory;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SimilarSiteCategoryResource extends Resource
{
    protected static ?string $model = SimilarSiteCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationGroup = 'Similar Sites';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
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
            'index' => ListSimilarSiteCategories::route('/'),
            'create' => CreateSimilarSiteCategory::route('/create'),
            'edit' => EditSimilarSiteCategory::route('/{record}/edit'),
        ];
    }
}
