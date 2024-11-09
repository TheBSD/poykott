<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResourceResource\Pages;
use App\Models\CompanyResources;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyResourceResource extends Resource
{
    protected static ?string $model = CompanyResources::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListCompanyResources::route('/'),
            'create' => Pages\CreateCompanyResource::route('/create'),
            'edit' => Pages\EditCompanyResource::route('/{record}/edit'),
        ];
    }
}
