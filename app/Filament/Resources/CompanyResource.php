<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'title'),
                Forms\Components\Select::make('exit_strategy_id')
                    ->relationship('exitStrategy', 'title'),
                Forms\Components\Select::make('funding_level_id')
                    ->relationship('fundingLevel', 'title'),
                Forms\Components\Select::make('company_size_id')
                    ->relationship('companySize', 'title'),
                Forms\Components\DateTimePicker::make('approved_at'),
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('slug')
                    ->required(),
                Forms\Components\TextInput::make('url')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('logo'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('valuation')
                    ->numeric(),
                Forms\Components\TextInput::make('exit_valuation')
                    ->numeric(),
                Forms\Components\TextInput::make('stock_symbol'),
                Forms\Components\TextInput::make('total_funding')
                    ->numeric(),
                Forms\Components\DatePicker::make('last_funding_date'),
                Forms\Components\TextInput::make('headquarter'),
                Forms\Components\DatePicker::make('founded_at'),
                Forms\Components\Textarea::make('office_locations')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('employee_count')
                    ->numeric(),
                Forms\Components\TextInput::make('stock_quote'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('exitStrategy.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fundingLevel.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('companySize.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('logo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('valuation')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('exit_valuation')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_symbol')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_funding')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_funding_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('headquarter')
                    ->searchable(),
                Tables\Columns\TextColumn::make('founded_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_quote')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
