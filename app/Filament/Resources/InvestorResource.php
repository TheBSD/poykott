<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlternativeResource\RelationManagers\ResourcesRelationManager;
use App\Filament\Resources\AuditsRelationManagerResource\RelationManagers\AuditsRelationManager;
use App\Filament\Resources\InvestorResource\Pages\CreateInvestor;
use App\Filament\Resources\InvestorResource\Pages\EditInvestor;
use App\Filament\Resources\InvestorResource\Pages\ListInvestors;
use App\Models\Investor;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;
use UnitEnum;

class InvestorResource extends Resource
{
    protected static ?string $model = Investor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|UnitEnum|null $navigationGroup = 'Investors';

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('slug')->required(),
                // Fieldset::make('logo')
                //    ->relationship('logo',
                //        condition: fn (?array $state): bool => filled($state['path']),
                //    )
                //    ->schema([
                //        Hidden::make('type')->default('logo'),
                //        FileUpload::make('path')->image(),
                //    ])->columnSpan(1)->columns(1),
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
                Textarea::make('description')->columnSpanFull(),
                TextInput::make('url'),
            ]);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // ImageColumn::make('logo.path')->circular(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('tagsRelation.name')->label('Tags')->badge()->searchable(),
                TextColumn::make('description')->searchable()->limit(50),
                TextColumn::make('url')->searchable(),
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
                TextColumn::make('created_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    #[Override]
    public static function getRelations(): array
    {
        return [
            ResourcesRelationManager::class,
            AuditsRelationManager::class,
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListInvestors::route('/'),
            'create' => CreateInvestor::route('/create'),
            'edit' => EditInvestor::route('/{record}/edit'),
        ];
    }
}
