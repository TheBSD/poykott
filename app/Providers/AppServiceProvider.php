<?php

namespace App\Providers;

use App\Models\Alternative;
use App\Models\Company;
use App\Models\Investor;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
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
        DB::prohibitDestructiveCommands($this->app->isProduction());

        URL::forceHttps($this->app->isProduction());

        Model::shouldBeStrict(! $this->app->isProduction());

        Relation::enforceMorphMap([
            'company' => Company::class,
            'person' => Person::class,
            'investor' => Investor::class,
            'alternative' => Alternative::class,
            'user' => User::class,
        ]);

    }
}
