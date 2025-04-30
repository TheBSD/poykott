<?php

namespace App\Filament\Resources;

use App\Enums\ResourceType;
use App\Filament\Resources\ResourceResource\Pages\CreateResource;
use App\Filament\Resources\ResourceResource\Pages\EditResource;
use App\Filament\Resources\ResourceResource\Pages\ListResources;
use App\Models\Resource as ResourceModel;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class ResourceResource extends Resource
{
    protected static ?string $model = ResourceModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')
                    ->options(ResourceType::class)
                    ->native(false)
                    ->required()
                    ->rules([Rule::enum(ResourceType::class)])
                    ->selectablePlaceholder(false),

                TextInput::make('url')
                    ->rules(['required', 'url', 'active_url']),

                Textarea::make('notes')
                    ->rules(['nullable', 'string', 'max:255'])
                    ->columnSpanFull(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?ResourceModel $record): string => $record?->created_at ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?ResourceModel $record): string => $record?->updated_at ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->searchable(),
                TextColumn::make('url')
                    ->html()
                    ->color('info')
                    ->formatStateUsing(function ($record): string {
                        return "<a href='{$record->url}' target='_blank' class='underline'>$record->url</a>";
                    })
                    ->disabledClick()
                    ->searchable(),

                TextColumn::make('archive_url')
                    ->label('Wayback Snapshot')
                    ->html()
                    ->formatStateUsing(function ($record): string {
                        return "<a href='{$record->archive_url}' target='_blank' class='underline'>Wayback link</a>";
                    })
                    ->color('info')
                    ->disabledClick(),

                TextColumn::make('created_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListResources::route('/'),
            'create' => CreateResource::route('/create'),
            'edit' => EditResource::route('/{record}/edit'),
        ];
    }
}
