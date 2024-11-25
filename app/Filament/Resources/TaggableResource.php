<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaggableResource\Pages\CreateTaggable;
use App\Filament\Resources\TaggableResource\Pages\EditTaggable;
use App\Filament\Resources\TaggableResource\Pages\ListTaggables;
use App\Models\Taggable;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
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
                Select::make('tag_id')
                    ->relationship('tag', 'name')
                    ->required(),
                TextInput::make('taggable_type')
                    ->required(),
                TextInput::make('taggable_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tag.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('taggable_type')
                    ->searchable(),
                TextColumn::make('taggable_id')
                    ->numeric()
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
            'index' => ListTaggables::route('/'),
            'create' => CreateTaggable::route('/create'),
            'edit' => EditTaggable::route('/{record}/edit'),
        ];
    }
}
