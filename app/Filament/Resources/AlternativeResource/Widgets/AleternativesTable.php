<?php

namespace App\Filament\Resources\AlternativeResource\Widgets;

use App\Models\Alternative;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AleternativesTable extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
        ->query(Alternative::query())
            ->columns([
                ImageColumn::make('logo.path')->circular(), 
                TextColumn::make('name')->searchable(), 
                IconColumn::make('approved_at')->label('Approved')
                    ->boolean(fn (Alternative $record): bool => $record->approved_at !== null),
                TextColumn::make('tags.name')->badge()->searchable(),
                TextColumn::make('url')
                    ->url(fn(Alternative $record) => $record->url)
                    ->color('info')
                    ->openUrlInNewTab()->searchable()->limit(50),
            ])
            ->paginated([5, 10, 25]);;
    }
}
