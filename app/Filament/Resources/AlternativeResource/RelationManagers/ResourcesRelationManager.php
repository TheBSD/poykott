<?php

declare(strict_types=1);

namespace App\Filament\Resources\AlternativeResource\RelationManagers;

use App\Enums\ResourceType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;

class ResourcesRelationManager extends RelationManager
{
    protected static string $relationship = 'resources';

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('url')->label('URL')->required(),
                Select::make('type')->options(ResourceType::class)->required(),
                Textarea::make('notes')->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('url')
            ->columns([
                TextColumn::make('url'),
                TextColumn::make('type'),
                TextColumn::make('notes')->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
