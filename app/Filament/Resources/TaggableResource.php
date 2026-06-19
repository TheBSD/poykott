<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\TaggableResource\Pages\CreateTaggable;
use App\Filament\Resources\TaggableResource\Pages\EditTaggable;
use App\Filament\Resources\TaggableResource\Pages\ListTaggables;
use App\Models\Taggable;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;

class TaggableResource extends Resource
{
    protected static ?string $model = Taggable::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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

    #[Override]
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
            ->recordActions([
                EditAction::make(),
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
            //
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListTaggables::route('/'),
            'create' => CreateTaggable::route('/create'),
            'edit' => EditTaggable::route('/{record}/edit'),
        ];
    }
}
