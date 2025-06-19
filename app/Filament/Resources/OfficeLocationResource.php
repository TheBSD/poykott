<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeLocationResource\Actions\MergeTwoOfficeLocationsAction;
use App\Filament\Resources\OfficeLocationResource\Pages\CreateOfficeLocation;
use App\Filament\Resources\OfficeLocationResource\Pages\EditOfficeLocation;
use App\Filament\Resources\OfficeLocationResource\Pages\ListOfficeLocations;
use App\Filament\Resources\OfficeLocationResource\RelationManagers\CompaniesRelationManager;
use App\Models\OfficeLocation;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OfficeLocationResource extends Resource
{
    protected static ?string $model = OfficeLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Companies';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->unique('office_locations', 'name'),
                TextInput::make('lat'),
                TextInput::make('lng'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null) // make record non-clickable
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('companies.name')->badge()->color('info')->searchable(),
                TextColumn::make('lat'),
                TextColumn::make('lng'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                MergeTwoOfficeLocationsAction::make('MergeWithAnother'),
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
            CompaniesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOfficeLocations::route('/'),
            'create' => CreateOfficeLocation::route('/create'),
            'edit' => EditOfficeLocation::route('/{record}/edit'),
        ];
    }
}
