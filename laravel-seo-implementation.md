# Laravel SEO Enhancement Guide

## Introduction

This guide outlines a comprehensive approach to implementing SEO best practices in your Laravel project. It covers meta tags, Open Graph, Twitter Cards, sitemap generation, robots.txt, canonical URLs, and dynamic title/description handling.

## Required Packages

```bash
composer require artesaos/seotools spatie/laravel-sitemap
```

## 1. Publish Configuration Files

```bash
php artisan vendor:publish --provider="Artesaos\SEOTools\Providers\SEOToolsServiceProvider"
php artisan vendor:publish --provider="Spatie\Sitemap\SitemapServiceProvider" --tag="config"
```

## 2. Create SEO Middleware

```php
// app/Http/Middleware/SEOMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;

class SEOMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        //shared variables
        $title = config('app.name', 'Boycott Israeli Tech');
        $description = 'Find ethical alternatives to Israeli tech products and companies';
        $url = url()->current();

        // Default SEO settings
        SEOMeta::setTitleSeparator(' - ');
        SEOMeta::setTitle($title);
        SEOMeta::setDescription($description);
        SEOMeta::setCanonical($url);
        SEOMeta::addMeta('robots', 'index,follow');

        // Open Graph
        OpenGraph::setTitle($title);
        OpenGraph::setDescription($description);
        OpenGraph::setUrl($url);
        OpenGraph::addProperty('type', 'website');
        OpenGraph::addProperty('locale', app()->getLocale());
        OpenGraph::addImage(asset('images/og-image.jpg'));

        // Twitter Card
        TwitterCard::setTitle($title);
        TwitterCard::setDescription($description);
        TwitterCard::setUrl($url);
        TwitterCard::setImage(asset('images/twitter-image.jpg'));
        TwitterCard::setType('summary_large_image');
        TwitterCard::setSite('@yourtwitterhandle');

        // JSON-LD
        JsonLd::setTitle($title);
        JsonLd::setDescription($description);
        JsonLd::setType('WebSite');
        JsonLd::addImage(asset('images/og-image.jpg'));

        return $next($request);
    }
}
```

## 3. Register Middleware in Kernel

```php
// app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \App\Http\Middleware\SEOMiddleware::class,
    ],
];
```

## 4. Create SEO Helper Class

```php
// app/Helpers/SEOHelper.php
namespace App\Helpers;

use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;

class SEOHelper
{
    /**
     * Set SEO data for a page
     */
    public static function setPage($title, $description, $image = null, $type = 'website')
    {
        $fullTitle = $title . ' - ' . config('app.name', 'Boycott Israeli Tech');

        SEOMeta::setTitle($title);
        SEOMeta::setDescription($description);

        OpenGraph::setTitle($fullTitle);
        OpenGraph::setDescription($description);
        OpenGraph::setType($type);

        TwitterCard::setTitle($fullTitle);
        TwitterCard::setDescription($description);

        JsonLd::setTitle($fullTitle);
        JsonLd::setDescription($description);
        JsonLd::setType($type);

        if ($image) {
            OpenGraph::addImage($image);
            TwitterCard::setImage($image);
            JsonLd::addImage($image);
        }
    }

    /**
     * Set SEO data for an alternative page
     */
    public static function setAlternative($alternative)
    {
        self::setPage(
            $alternative->name,
            $alternative->description,
            $alternative->getFirstMediaUrl() ?: asset('images/default-alternative.jpg'),
            'product'
        );

        // Add structured data for product
        JsonLd::setType('Product');
        JsonLd::addValue('name', $alternative->name);
        JsonLd::addValue('description', $alternative->description);

        // Add tags as keywords
        if ($alternative->tagsRelation && $alternative->tagsRelation->count() > 0) {
            $keywords = $alternative->tagsRelation->pluck('name')->implode(', ');
            SEOMeta::addKeyword($keywords);
        }
    }

    /**
     * Set SEO data for a company page
     */
    public static function setCompany($company)
    {
        self::setPage(
            $company->name,
            $company->description,
            $company->getFirstMediaUrl() ?: asset('images/default-company.jpg'),
            'organization'
        );

        // Add structured data for organization
        JsonLd::setType('Organization');
        JsonLd::addValue('name', $company->name);
        JsonLd::addValue('description', $company->description);

        if ($company->website) {
            JsonLd::addValue('url', $company->website);
        }
    }
}
```

## 5. Register Helper in `composer.json`

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    },
    "files": [
        "app/Helpers/SEOHelper.php"
    ]
}
```

Then run:

```bash
composer dump-autoload
```

## 6. Update Layout File with SEO Tags

```blade
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        {!! SEO::generate() !!}

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}" />

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body>
        <!-- Content -->
    </body>
</html>
```

## 7. Implement in Controllers

```php
// app/Http/Controllers/HomeController.php
public function show(Alternative $alternative)
{
    \App\Helpers\SEOHelper::setAlternative($alternative);

    return view('pages.alternative', compact('alternative'));
}

// app/Http/Controllers/CompanyController.php
public function show(Company $company)
{
    \App\Helpers\SEOHelper::setCompany($company);

    return view('pages.company', compact('company'));
}

// For other pages
public function about()
{
    \App\Helpers\SEOHelper::setPage(
        'About Us',
        'Learn more about our mission to provide ethical alternatives to Israeli tech products.',
        asset('images/about-us.jpg')
    );

    return view('pages.about');
}
```

## 8. Create Sitemap Generator Command

```php
// app/Console/Commands/GenerateSitemap.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Alternative;
use App\Models\Company;
use App\Models\Person;
use App\Models\Investor;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap';

    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create()
            ->add(Url::create('/'))
            ->add(Url::create('/alternatives'))
            ->add(Url::create('/companies'))
            ->add(Url::create('/about'))
            ->add(Url::create('/faqs'))
            ->add(Url::create('/contact'))
            ->add(Url::create('/newsletter'))
            ->add(Url::create('/similar-sites'));

        // Add all alternatives
        $this->info('Adding alternatives to sitemap...');
        Alternative::approved()->chunk(100, function ($alternatives) use ($sitemap) {
            $sitemap->add(
                Url::create("/alternatives/{$alternatives->slug}")
                    ->setLastModificationDate($alternatives->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8)****
            );
        });

        // Add all companies
        $this->info('Adding companies to sitemap...');
        Company::approved()->chunk(100, function ($companies) use ($sitemap) {
            $sitemap->add(
                Url::create("/companies/{$company->slug}")
                    ->setLastModificationDate($company->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8)
            );
        });

        // Add people
        $this->info('Adding people to sitemap...');
        Person::approved()->chunk(100, function ($people) use ($sitemap) {
            $sitemap->add(
                Url::create("/people/{$person->slug}")
                    ->setLastModificationDate($person->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.7)
            );
        });

        // Add investors
        $this->info('Adding investors to sitemap...');
        Investor::approved()->chunk(100, function ($investors) use ($sitemap) {
            $sitemap->add(
                Url::create("/investors/{$investor->slug}")
                    ->setLastModificationDate($investor->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.7)
            );
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));
        $this->info('Sitemap generated successfully!');
    }
}
```

## 9. Schedule Sitemap Generation

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('sitemap:generate')->daily();
}
```

## 10. Create robots.txt

```
# public/robots.txt
User-agent: *
Allow: /

Sitemap: https://yourdomain.com/sitemap.xml
```

## 11. Additional SEO Improvements

### Create a Blade Component for Structured Data

```php
// app/View/Components/StructuredData.php
namespace App\View\Components;

use Illuminate\View\Component;

class StructuredData extends Component
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function render()
    {
        return view('components.structured-data');
    }
}
```

```blade
<!-- resources/views/components/structured-data.blade.php -->
<script type="application/ld+json">
    {!! json_encode($data) !!}
</script>
```

### Create a Meta Tag Manager

```php
// app/Helpers/MetaTagManager.php
namespace App\Helpers;

class MetaTagManager
{
    protected static $tags = [];

    public static function add($name, $content)
    {
        static::$tags[$name] = $content;
    }

    public static function get($name)
    {
        return static::$tags[$name] ?? null;
    }

    public static function all()
    {
        return static::$tags;
    }

    public static function render()
    {
        $html = '';
        foreach (static::$tags as $name => $content) {
            $html .= "<meta name=\"{$name}\" content=\"{$content}\">\n";
        }
        return $html;
    }
}
```

## 12. Implement Breadcrumbs for SEO

Install the breadcrumbs package:

```bash
composer require diglactic/laravel-breadcrumbs
```

Create breadcrumb definitions:

```php
// routes/breadcrumbs.php
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home'));
});

// Home > Alternatives
Breadcrumbs::for('alternatives.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Alternatives', route('alternatives.index'));
});

// Home > Alternatives > [Alternative]
Breadcrumbs::for('alternatives.show', function (BreadcrumbTrail $trail, $alternative) {
    $trail->parent('alternatives.index');
    $trail->push($alternative->name, route('alternatives.show', $alternative));
});

// Home > Companies
Breadcrumbs::for('companies.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Companies', route('companies.index'));
});

// Home > Companies > [Company]
Breadcrumbs::for('companies.show', function (BreadcrumbTrail $trail, $company) {
    $trail->parent('companies.index');
    $trail->push($company->name, route('companies.show', $company));
});
```

Add breadcrumbs to your views:

```blade
{{ Breadcrumbs::render('alternatives.show', $alternative) }}
```

## 13. Implement Structured Data for FAQ Pages

```php
// In your FAQs controller
public function index()
{
    $faqs = FAQ::all();

    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => []
    ];

    foreach ($faqs as $faq) {
        $faqSchema['mainEntity'][] = [
            '@type' => 'Question',
            'name' => $faq->question,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq->answer
            ]
        ];
    }

    return view('pages.faqs', [
        'faqs' => $faqs,
        'faqSchema' => $faqSchema
    ]);
}
```

In your view:

```blade
<x-structured-data :data="$faqSchema" />
```

## 14. Implement Hreflang Tags for Multi-language Support

If your site supports multiple languages:

```php
// In your SEOMiddleware
public function handle(Request $request, Closure $next)
{
    // ... other SEO settings

    // Add hreflang tags for supported languages
    $supportedLocales = ['en', 'ar', 'fr'];
    $currentUrl = url()->current();

    foreach ($supportedLocales as $locale) {
        $localizedUrl = str_replace('/' . app()->getLocale() . '/', '/' . $locale . '/', $currentUrl);
        SEOMeta::addAlternateLanguage($locale, $localizedUrl);
    }

    return $next($request);
}
```

## 15. Performance Optimization for SEO

Add caching to your SEO data:

```php
// In SEOHelper.php
public static function setAlternative($alternative)
{
    $cacheKey = "seo_alternative_{$alternative->id}";

    if (Cache::has($cacheKey)) {
        $seoData = Cache::get($cacheKey);

        SEOMeta::setTitle($seoData['title']);
        SEOMeta::setDescription($seoData['description']);
        // Set other cached values

        return;
    }

    // Set SEO data as before
    self::setPage(
        $alternative->name,
        $alternative->description,
        $alternative->getFirstMediaUrl() ?: null,
        'product'
    );

    // Cache the data
    Cache::put($cacheKey, [
        'title' => $alternative->name,
        'description' => $alternative->description,
        // Other SEO data
    ], now()->addDay());
}
```

## 16. Accessibility Improvements

Ensure all form elements have proper labels:

```blade
<!-- Before -->
<select wire:model.live="order" class="...">
    <option value="">Order by</option>
    <!-- options -->
</select>

<!-- After -->
<label for="order-select" class="sr-only">Order by</label>
<select id="order-select" wire:model.live="order" class="...">
    <option value="">Order by</option>
    <!-- options -->
</select>
```

## 17. SEO Testing and Monitoring

Consider using these tools to monitor your SEO implementation:

- Google Search Console
- Google Analytics
- Lighthouse in Chrome DevTools
- SEO Spider by Screaming Frog

## Conclusion

By implementing these SEO best practices in your Laravel project, you'll significantly improve your site's visibility in search engines and provide a better experience for users. Remember to regularly review and update your SEO strategy based on performance data and evolving best practices.
