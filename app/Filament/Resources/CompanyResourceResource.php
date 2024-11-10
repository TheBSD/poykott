<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResourceResource\Pages;
use App\Models\CompanyResources;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompanyResourceResource extends Resource
{
    protected static ?string $model = CompanyResources::class;

    protected static ?string $navigationIcon = 'heroicon-o-cursor-arrow-ripple';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->required(),
                TextInput::make('url')->required(),
                Select::make('company_id')
                    ->searchable()->preload()
                    ->relationship('company', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')->numeric()->sortable()->searchable(),
                TextColumn::make('title')->searchable()->sortable(), 
                TextColumn::make('url')
                ->url(fn(CompanyResources $record) => $record->url)
                ->color('info')
                ->openUrlInNewTab()->searchable()->limit(50), 
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
            'index' => Pages\ListCompanyResources::route('/'),
            'create' => Pages\CreateCompanyResource::route('/create'),
            'edit' => Pages\EditCompanyResource::route('/{record}/edit'),
        ];
    }
}
