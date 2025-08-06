<?php

namespace App\Providers;

use App\Models\Alternative;
use App\Models\Company;
use App\Models\Investor;
use App\Models\Person;
use App\Models\SimilarSite;
use App\Models\Tag;
use App\Models\User;
use BezhanSalleh\FilamentShield\FilamentShield;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Date;
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
        DB::prohibitDestructiveCommands(app()->isProduction());
        FilamentShield::prohibitDestructiveCommands(app()->isProduction());

        URL::forceHttps(app()->isProduction());

        Model::shouldBeStrict(! app()->isProduction());

        Date::use(CarbonImmutable::class);

        Relation::enforceMorphMap([
            'company' => Company::class,
            'person' => Person::class,
            'investor' => Investor::class,
            'alternative' => Alternative::class,
            'user' => User::class,
            'tag' => Tag::class,
            'similar_site' => SimilarSite::class,
        ]);
    }
}
