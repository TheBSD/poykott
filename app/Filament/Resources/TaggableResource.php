<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaggableResource\Pages;
use App\Models\Taggable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaggableResource extends Resource
{
    protected static ?string $model = Taggable::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tag_id')
                    ->relationship('tag', 'name')
                    ->required(),
                Forms\Components\TextInput::make('taggable_type')
                    ->required(),
                Forms\Components\TextInput::make('taggable_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tag.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('taggable_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('taggable_id')
                    ->numeric()
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
            'index' => Pages\ListTaggables::route('/'),
            'create' => Pages\CreateTaggable::route('/create'),
            'edit' => Pages\EditTaggable::route('/{record}/edit'),
        ];
    }
}
