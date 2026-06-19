<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialLinkResource\Pages\CreateSocialLink;
use App\Filament\Resources\SocialLinkResource\Pages\EditSocialLink;
use App\Filament\Resources\SocialLinkResource\Pages\ListSocialLinks;
use App\Models\Company;
use App\Models\Person;
use App\Models\SocialLink;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Override;

class SocialLinkResource extends Resource
{
    protected static ?string $model = SocialLink::class;

    protected static ?string $slug = 'social-links';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                                    ->mapWithKeys(fn ($company): array => [
                                        'company|' . $company->id => 'Company: ' . $company->name,
                                    ])
                            )
                            ->merge(
                                Person::query()
                                    ->select('id', 'name')
                                    ->get()
                                    ->mapWithKeys(fn ($person): array => [
                                        'person|' . $person->id => 'Person: ' . $person->name,
                                    ])
                            )
                            ->all();
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

    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('linkable.name')
                    ->searchable(),

                TextColumn::make('url'),
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
    public static function getPages(): array
    {
        return [
            'index' => ListSocialLinks::route('/'),
            'create' => CreateSocialLink::route('/create'),
            'edit' => EditSocialLink::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['linkable']);
    }

    #[Override]
    public static function getGloballySearchableAttributes(): array
    {
        return ['linkable.name'];
    }

    #[Override]
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->linkable) {
            $details['Linkable'] = $record->linkable->name;
        }

        return $details;
    }
}
