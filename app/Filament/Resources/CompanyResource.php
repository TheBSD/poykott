<?php

namespace App\Filament\Resources;

use App\Actions\GenerateCompanyAiAlternativesAction;
use App\Actions\ScrapeLogoFromUrlAction;
use App\Filament\Resources\AlternativeResource\RelationManagers\ResourcesRelationManager;
use App\Filament\Resources\AuditsRelationManagerResource\RelationManagers\AuditsRelationManager;
use App\Filament\Resources\CompanyResource\Pages\CreateCompany;
use App\Filament\Resources\CompanyResource\Pages\EditCompany;
use App\Filament\Resources\CompanyResource\Pages\ListCompanies;
use App\Filament\Resources\CompanyResource\RelationManagers\AlternativesRelationManager;
use App\Filament\Resources\CompanyResource\RelationManagers\OfficeLocationsRelationManager;
use App\Models\Company;
use BackedEnum;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Override;
use UnitEnum;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|UnitEnum|null $navigationGroup = 'Companies';

    #[Override]
    public static function form(Schema $schema): Schema
    {

        return $schema
            ->components([
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

    #[Override]
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
                TernaryFilter::make('has_alternatives')
                    ->label('Has alternatives')
                    ->queries(
                        true: fn ($query) => $query->whereHas('alternatives'),
                        false: fn ($query) => $query->whereDoesntHave('alternatives'),
                    ),
            ])
            ->recordActions([
                Action::make('fetchLogo')
                    ->label('Fetch Logo')
                    ->action(function (Company $record): void {

                        $success = resolve(ScrapeLogoFromUrlAction::class)->execute($record, $record->url);

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

                Action::make('regenerateAiAlternatives')
                    ->label('Regenerate AI Alternatives')
                    ->icon('heroicon-o-sparkles')
                    ->action(function (Company $record): void {
                        $record->aiAlternative?->delete();

                        try {
                            resolve(GenerateCompanyAiAlternativesAction::class)->execute($record);

                            Notification::make()
                                ->success()
                                ->title('AI alternatives regenerated successfully')
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Failed to regenerate AI alternatives')
                                ->body($e->getMessage())
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Regenerate AI Alternatives')
                    ->modalDescription('This will delete existing AI-generated alternatives and create new ones.')
                    ->color('warning'),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
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
            ])
            ->persistFiltersInSession();
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            AlternativesRelationManager::class,
            ResourcesRelationManager::class,
            OfficeLocationsRelationManager::class,
            AuditsRelationManager::class,
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'edit' => EditCompany::route('/{record}/edit'),
        ];
    }
}
