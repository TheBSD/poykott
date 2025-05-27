<?php

namespace App\Filament\Resources;

use App\Actions\ScrapeLogoFromUrlAction;
use App\Filament\Resources\AlternativeResource\RelationManagers\ResourcesRelationManager;
use App\Filament\Resources\CompanyResource\Pages\CreateCompany;
use App\Filament\Resources\CompanyResource\Pages\EditCompany;
use App\Filament\Resources\CompanyResource\Pages\ListCompanies;
use App\Filament\Resources\CompanyResource\RelationManagers\AlternativesRelationManager;
use App\Filament\Resources\CompanyResource\RelationManagers\OfficeLocationsRelationManager;
use App\Models\Company;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Companies';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->unique(table: 'companies', column: 'name', ignoreRecord: true)
                    ->required(),
                TextInput::make('slug'),
                TextInput::make('url')->required(),
                DateTimePicker::make('approved_at'),
                Textarea::make('description')->columnSpanFull(),
                Textarea::make('notes')->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->rule([
                        'image',
                        'mimes:jpeg,jpg,png,svg,webp',
                        'max:2048',  // 2MB limit
                    ]),
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
                TextInput::make('headquarter'),
                TextInput::make('valuation')->numeric()->nullable(),
                TextInput::make('exit_valuation')->numeric()->nullable(),
                TextInput::make('exit_strategy')->nullable(),
                TextInput::make('stock_symbol'),
                TextInput::make('total_funding')->nullable(),
                DatePicker::make('last_funding_date'),
                TextInput::make('funding_stage')->nullable(),
                DatePicker::make('founded_at')->format('Y'),
                TextInput::make('employee_count')->numeric(),
                TextInput::make('stock_quote'),
                Repeater::make('socialLinks')
                    ->label('Social Links')
                    ->relationship('socialLinks')
                    ->schema([
                        TextInput::make('url')
                            ->label('Social URL')
                            ->url()
                            ->required()
                            ->distinct(),
                    ])
                    ->defaultItems(0)
                    ->addActionLabel('Add Social Link')
                    ->columnSpanFull()
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')->size(70),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('tagsRelation.name')->label('Tags')->badge()->searchable(),
                TextColumn::make('officeLocations.name')->badge()->color('info')->searchable(),
                TextColumn::make('url')
                    ->url(fn (Company $record) => $record->url)
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
                IconColumn::make('approved_at')->label('Approved')
                    ->boolean(fn (Company $record): bool => $record->approved_at !== null),
                TextColumn::make('valuation')->numeric()->sortable(),
                TextColumn::make('exit_valuation')->numeric()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('exitStrategy.title')->numeric()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stock_symbol')->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_funding')->numeric()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_funding_date')->date()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('funding_stage')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('headquarter')->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('founded_at')->sortable()
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
                Action::make('fetchLogo')
                    ->label('Fetch Logo')
                    ->action(function (Company $record): void {

                        $success = app(ScrapeLogoFromUrlAction::class)->execute($record, $record->url);

                        if (! $success) {
                            Notification::make()
                                ->danger()
                                ->title('Failed fetching logo. Try uploading the logo manually')
                                ->send();
                        } else {
                            Notification::make()
                                ->success()
                                ->title('Logo fetched')
                                ->send();
                        }
                    })
                    ->requiresConfirmation(function ($record): bool {
                        return $record->media->count() > 0;
                    })
                    ->color('gray'),

                Action::make('removeLogo')
                    ->label('Remove Logo')
                    ->action(function (Company $record): void {
                        $record->clearMediaCollection();
                        Notification::make()
                            ->success()
                            ->title('Logo removed')
                            ->send();
                    })
                    ->requiresConfirmation(function ($record): bool {
                        return $record->media->count() > 0;
                    })
                    ->visible(function ($record): bool {
                        return $record->media->count() > 0;
                    })
                    ->color('danger'),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('approve')
                        ->icon('heroicon-m-check-circle')
                        ->modalIcon('heroicon-m-check-circle')
                        ->color('success')
                        ->modalHeading('Are you sure you want to approve these companies?')
                        ->modalSubmitActionLabel('Approve')
                        ->successNotificationTitle('Companies Approved')
                        ->action(function (Collection $records, array $data): void {
                            Company::query()->whereIn('id', $records->pluck('id'))->update(['approved_at' => now()]);

                            Notification::make()
                                ->success()
                                ->title('Companies Approved')
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AlternativesRelationManager::class,
            ResourcesRelationManager::class,
            OfficeLocationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'edit' => EditCompany::route('/{record}/edit'),
        ];
    }
}
