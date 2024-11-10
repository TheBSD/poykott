<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResourcesResource\Pages;
use App\Models\PersonResources;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PersonResourcesResource extends Resource
{
    protected static ?string $model = PersonResources::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('person_id')
                    ->relationship('person', 'full_name')
                    ->searchable()->preload()->required(),
                TextInput::make('title')->required(),
                TextInput::make('url')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('person.full_name')->numeric()->sortable(),
                TextColumn::make('title')->searchable(),
                TextColumn::make('url')->searchable(),
                TextColumn::make('created_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPersonResources::route('/'),
            'create' => Pages\CreatePersonResources::route('/create'),
            'edit' => Pages\EditPersonResources::route('/{record}/edit'),
        ];
    }
}
