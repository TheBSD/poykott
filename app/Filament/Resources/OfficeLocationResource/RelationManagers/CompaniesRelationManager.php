<?php

declare(strict_types=1);

namespace App\Filament\Resources\OfficeLocationResource\RelationManagers;

use App\Models\Company;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;

class CompaniesRelationManager extends RelationManager
{
    protected static string $relationship = 'companies';

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('url')
                    ->url(fn (Company $record) => $record->url)
                    ->color('info')
                    ->openUrlInNewTab()->searchable()->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()->preloadRecordSelect(),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
