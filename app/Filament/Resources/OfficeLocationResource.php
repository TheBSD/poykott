<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\AuditsRelationManagerResource\RelationManagers\AuditsRelationManager;
use App\Filament\Resources\OfficeLocationResource\Actions\MergeTwoOfficeLocationsAction;
use App\Filament\Resources\OfficeLocationResource\Pages\CreateOfficeLocation;
use App\Filament\Resources\OfficeLocationResource\Pages\EditOfficeLocation;
use App\Filament\Resources\OfficeLocationResource\Pages\ListOfficeLocations;
use App\Filament\Resources\OfficeLocationResource\RelationManagers\CompaniesRelationManager;
use App\Models\OfficeLocation;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;
use UnitEnum;

class OfficeLocationResource extends Resource
{
    protected static ?string $model = OfficeLocation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static string|UnitEnum|null $navigationGroup = 'Companies';

    protected static ?int $perPage = 10;

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->unique('office_locations', 'name'),
                TextInput::make('lat'),
                TextInput::make('lng'),
            ]);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null) // make record non-clickable
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('old_name')->sortable()->searchable()->limit(50),
                TextColumn::make('companies.name')->badge()->color('info')->searchable(),
                TextColumn::make('lat'),
                TextColumn::make('lng'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                MergeTwoOfficeLocationsAction::make('MergeWithAnother'),
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
            CompaniesRelationManager::class,
            AuditsRelationManager::class,
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListOfficeLocations::route('/'),
            'create' => CreateOfficeLocation::route('/create'),
            'edit' => EditOfficeLocation::route('/{record}/edit'),
        ];
    }
}
