<?php

namespace App\Providers;

use App\Models\Alternative;
use App\Models\Company;
use App\Models\ContactMessage;
use App\Models\Investor;
use App\Models\Person;
use App\Models\Resource;
use App\Models\SimilarSite;
use App\Models\Tag;
use App\Models\User;
use App\Policies\AuditPolicy;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Carbon\CarbonImmutable;
use Filament\Pages\Page;
use Filament\Resources\Resource as FilamentResource;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Override;
use OwenIt\Auditing\Models\Audit;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
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
        FilamentShield::buildPermissionKeyUsing(function (string $entity, string $affix, string $subject): string {
            if (is_subclass_of($entity, FilamentResource::class)) {
                return str($affix)->snake() . '_' . str($subject)->snake('::');
            }

            if (is_subclass_of($entity, Page::class)) {
                return 'page_' . $subject;
            }

            if (is_subclass_of($entity, Widget::class)) {
                return 'widget_' . $subject;
            }

            return str($affix)->snake() . '_' . $subject;
        });

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
            'contact_message' => ContactMessage::class,
            'resource' => Resource::class,
        ]);

        // just for \OwenIt\Auditing\Models\Audit::class
        Gate::policy(Audit::class, AuditPolicy::class);

    }
}
