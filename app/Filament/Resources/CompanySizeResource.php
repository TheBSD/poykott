<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanySizeResource\Pages\CreateCompanySize;
use App\Filament\Resources\CompanySizeResource\Pages\EditCompanySize;
use App\Filament\Resources\CompanySizeResource\Pages\ListCompanySizes;
use App\Models\CompanySize;
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

class CompanySizeResource extends Resource
{
    protected static ?string $model = CompanySize::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->required(),
                Textarea::make('description')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('description')->searchable()->limit(50),
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
            'index' => ListCompanySizes::route('/'),
            'create' => CreateCompanySize::route('/create'),
            'edit' => EditCompanySize::route('/{record}/edit'),
        ];
    }
}
