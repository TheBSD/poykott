<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlternativeResource\RelationManagers\ResourcesRelationManager;
use App\Filament\Resources\PersonResource\Pages;
use App\Models\Person;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('avatar'),
                TextInput::make('slug')->required(),
                TextInput::make('job_title'),
                DateTimePicker::make('approved_at'),
                TextInput::make('location'),
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
                Textarea::make('social_links')->columnSpanFull()
                    ->formatStateUsing(function ($state) {
                        return implode(',', $state);
                    })
                    ->placeholder('Enter links separated by commas')
                    ->helperText('Enter each link separated by a comma (e.g., https://link1.com, https://link2.com)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                ImageColumn::make('avatar')->circular(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('tagsRelation.name')->label('Tags')->badge()->searchable(),
                TextColumn::make('job_title')->searchable()->sortable(),
                IconColumn::make('approved_at')->label('Approved')
                    ->boolean(fn (Person $record): bool => $record->approved_at !== null),
                TextColumn::make('resources.url')
                    ->label('Resources')
                    ->formatStateUsing(function ($record) {
                        return $record->resources->map(function ($resource) {
                            return "<a href='{$resource->url}' target='_blank'>" . Str::limit($resource->url, 50) . '</a>';
                        })->implode('<br>');
                    })
                    ->html()
                    ->disabledClick()
                    ->color('info'),
                TextColumn::make('location')->searchable()->sortable(),
                TextColumn::make('social_links')
                    ->formatStateUsing(function (Person $record) {
                        $links = $record->social_links ?? [];

                        $formattedLinks = array_map(function ($name, $url) {
                            return "<a href='{$url}' class='text-blue-500 target='_blank'>{$url}</a>";
                        }, array_keys($links), $links);

                        return implode('<br>', $formattedLinks);
                    })
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('biography')->searchable()->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(''),
                Tables\Actions\DeleteAction::make()->label(''),
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
            ResourcesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
}
