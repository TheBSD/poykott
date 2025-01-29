<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlternativeResource\Pages\CreateAlternative;
use App\Filament\Resources\AlternativeResource\Pages\EditAlternative;
use App\Filament\Resources\AlternativeResource\Pages\ListAlternatives;
use App\Filament\Resources\AlternativeResource\RelationManagers\CompaniesRelationManager;
use App\Filament\Resources\AlternativeResource\RelationManagers\ResourcesRelationManager;
use App\Models\Alternative;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AlternativeResource extends Resource
{
    protected static ?string $model = Alternative::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Alternatives';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('url')->required(),
            Textarea::make('description')->columnSpanFull(),
            Textarea::make('notes')->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('logo')->rule(['image', 'mimes:jpeg,jpg,png,svg,webp']),
            Select::make('tags')->relationship('tagsRelation', 'name')
                ->multiple()->searchable()->preload()->native(false)
                ->createOptionForm([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('slug')
                            ->required(),
                    ]),
                ]),
            DateTimePicker::make('approved_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')->size(70),
                TextColumn::make('name')->sortable()->searchable(),
                IconColumn::make('approved_at')
                    ->label('Approved')
                    ->boolean(fn (Alternative $record): bool => $record->approved_at !== null)
                    ->sortable(),
                TextColumn::make('tagsRelation.name')->label('Tags')->badge()->searchable(),
                TextColumn::make('url')
                    ->url(fn (Alternative $record) => $record->url)
                    ->color('info')
                    ->openUrlInNewTab()->searchable()->limit(50),
                TextColumn::make('resources.url')
                    ->label('Resources')
                    ->formatStateUsing(function ($record) {
                        return $record->resources->map(function ($resource): string {
                            return "<a href='{$resource->url}' target='_blank'>{$resource->url}</a>";
                        })->implode('<br>');
                    })
                    ->html()
                    ->disabledClick()
                    ->color('info'),
                TextColumn::make('description')->limit(50),
                TextColumn::make('notes')->limit(50),
                TextColumn::make('created_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()->label(''),
                DeleteAction::make()->label(''),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            ResourcesRelationManager::class,
            CompaniesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlternatives::route('/'),
            'create' => CreateAlternative::route('/create'),
            'edit' => EditAlternative::route('/{record}/edit'),
        ];
    }
}
