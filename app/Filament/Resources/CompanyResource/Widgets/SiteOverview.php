<?php

namespace App\Filament\Resources\CompanyResource\Widgets;

use App\Models\Alternative;
use App\Models\Company;
use App\Models\Investor;
use App\Models\Person;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        return [
            Stat::make('Companies', Company::query()->count())
                ->description('Companies count')
                ->descriptionIcon('heroicon-m-building-office-2', IconPosition::Before)
                ->color('danger'),
            Stat::make('Alternatives', Alternative::query()->count())
                ->description('Alternatives count')
                ->descriptionIcon('heroicon-m-arrows-right-left', IconPosition::Before)
                ->color('success'),
            Stat::make('Investors', Investor::query()->count())
                ->description('Investors count')
                ->descriptionIcon('heroicon-m-banknotes', IconPosition::Before)
                ->color('warning'),
            Stat::make('People', Person::query()->count())
                ->description('People count')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->color('info'),
        ];
    }
}
