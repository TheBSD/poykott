<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialLinkResource\Pages\CreateSocialLink;
use App\Filament\Resources\SocialLinkResource\Pages\EditSocialLink;
use App\Filament\Resources\SocialLinkResource\Pages\ListSocialLinks;
use App\Models\Company;
use App\Models\Person;
use App\Models\SocialLink;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class SocialLinkResource extends Resource
{
    protected static ?string $model = SocialLink::class;

    protected static ?string $slug = 'social-links';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('linkable_type')
                    ->required(),

                Hidden::make('linkable_id')
                    ->required(),

                Select::make('linkable')
                    ->label('Linkable')
                    ->searchable()
                    ->options(function () {
                        return collect()
                            ->merge(
                                Company::query()
                                    ->select('id', 'name')
                                    ->get()
                                    ->mapWithKeys(fn ($company) => [
                                        'company|' . $company->id => 'Company: ' . $company->name,
                                    ])
                            )
                            ->merge(
                                Person::query()
                                    ->select('id', 'name')
                                    ->get()
                                    ->mapWithKeys(fn ($person) => [
                                        'person|' . $person->id => 'Person: ' . $person->name,
                                    ])
                            )
                            ->toArray();
                    })
                    ->afterStateUpdated(function ($state, callable $set): void {
                        if ($state && str_contains($state, '|')) {
                            [$type, $id] = explode('|', $state);
                            $set('linkable_type', $type);
                            $set('linkable_id', $id);
                        }
                    })
                    ->formatStateUsing(function ($state, $record) {
                        if ($record && $record->linkable_type && $record->linkable_id) {
                            return $record->linkable_type . '|' . $record->linkable_id;
                        }

                        return $state;
                    })
                    ->dehydrated(false)
                    ->required(),

                TextInput::make('url')
                    ->required()
                    ->url()
                    ->rules(function (callable $get, $record): array {
                        return [
                            Rule::unique('social_links', 'url')
                                ->ignore($record?->id)
                                ->where(function ($query) use ($record) {
                                    return $query
                                        ->where('linkable_type', $record?->linkable_type)
                                        ->where('linkable_id', $record?->linkable_id);
                                }),
                        ];
                    }),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?SocialLink $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?SocialLink $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('linkable.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('url'),
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

    public static function getPages(): array
    {
        return [
            'index' => ListSocialLinks::route('/'),
            'create' => CreateSocialLink::route('/create'),
            'edit' => EditSocialLink::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['linkable']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['linkable.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->linkable) {
            $details['Linkable'] = $record->linkable->name;
        }

        return $details;
    }
}
