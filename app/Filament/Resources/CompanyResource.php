<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('slug')->required(),
                TextInput::make('url')->required(),
                DateTimePicker::make('approved_at'),
                Textarea::make('description')->columnSpanFull(),
                Textarea::make('notes')->columnSpanFull(),
                TextInput::make('logo'),
                TextInput::make('headquarter'),
                TextInput::make('valuation')->numeric(),
                TextInput::make('exit_valuation')->numeric(),
                Select::make('category_id')
                    ->relationship('category', 'title'),
                Select::make('exit_strategy_id')
                    ->relationship('exitStrategy', 'title'),
                Select::make('funding_level_id')
                    ->relationship('fundingLevel', 'title'),
                Select::make('company_size_id')
                    ->relationship('companySize', 'title'),
                TextInput::make('stock_symbol'),
                TextInput::make('total_funding')->numeric(),
                DatePicker::make('last_funding_date'),
                DatePicker::make('founded_at'),
                Textarea::make('office_locations'),
                TextInput::make('employee_count')->numeric(),
                TextInput::make('stock_quote'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('url')->searchable(),
                IconColumn::make('approved_at')->label('Approved')
                ->boolean(fn (Company $record): bool => $record->approved_at !== null),
                TextColumn::make('logo')->limit(50)->searchable(),
                TextColumn::make('valuation')->numeric()->sortable(),
                TextColumn::make('category.title')->numeric()->sortable(),
                TextColumn::make('exit_valuation')->numeric()->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('exitStrategy.title')->numeric()->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fundingLevel.title')->numeric()->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('companySize.title')->numeric()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stock_symbol')->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_funding')->numeric()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_funding_date')->date()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('headquarter')->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('founded_at')->date()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('employee_count')->numeric()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stock_quote')->searchable()
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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