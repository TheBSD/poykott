<?php

namespace App\Filament\Resources\AlternativeResource\RelationManagers;

use App\Enums\ResourceType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResourcesRelationManager extends RelationManager
{
    protected static string $relationship = 'resources';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('url')->label('URL')->required(),
                TextInput::make('title')->required(),
                Select::make('type')->options(ResourceType::class)->required(),
                Textarea::make('description')->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('url')
            ->columns([
                TextColumn::make('url'),
                TextColumn::make('title'),
                TextColumn::make('type'),
                TextColumn::make('description')->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
