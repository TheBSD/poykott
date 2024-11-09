<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Investor;
use App\Models\Person;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict();

        DB::prohibitDestructiveCommands($this->app->isProduction());

        Relation::enforceMorphMap([
            'company' => Company::class,
            'person' => Person::class,
            'investor' => Investor::class,
        ]);
    }
}
