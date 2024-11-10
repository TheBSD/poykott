<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('full_name')->required(),
                TextInput::make('avatar'),
                TextInput::make('slug')->required(),
                TextInput::make('job_title'),
                DateTimePicker::make('approved_at'),
                TextInput::make('location'),
                Textarea::make('biography')->columnSpanFull(),
                Textarea::make('social_links')->columnSpanFull()
                    ->placeholder('Enter links separated by commas')
                    ->helperText('Enter each link separated by a comma (e.g., https://link1.com, https://link2.com)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->searchable()->sortable(),
                ImageColumn::make('avatar')->searchable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('job_title')->searchable()->sortable(),
                IconColumn::make('approved_at')->boolean()->sortable(),
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
            //
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
