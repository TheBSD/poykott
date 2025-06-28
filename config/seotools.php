<?php

/**
 * @see https://github.com/artesaos/seotools
 */

return [
    'inertia' => env('SEO_TOOLS_INERTIA', false),
    'meta' => [
        /*
         * The default configurations to be used by the meta generator.
         */
        'defaults' => [
            'title' => config('app.name', 'Boycott Israeli Tech'), // set false to total remove
            'titleBefore' => false, // Put defaults.title before page title, like 'It's Over 9000! - Dashboard'
            'description' => 'Find ethical alternatives to Israeli tech products and companies', // set false to total remove
            'separator' => ' - ',
            'keywords' => [],
            'canonical' => 'current', // Set to null or 'full' to use Url::full(), set to 'current' to use Url::current(), set false to total remove
            'robots' => 'index,follow', // Set to 'all', 'none' or any combination of index/noindex and follow/nofollow
        ],
        /*
         * Webmaster tags are always added.
         */
        'webmaster_tags' => [
            'google' => null,
            'bing' => null,
            'alexa' => null,
            'pinterest' => null,
            'yandex' => null,
            'norton' => null,
        ],

        'add_notranslate_class' => false,
    ],
    'opengraph' => [
        /*
         * The default configurations to be used by the opengraph generator.
         */
        'defaults' => [
            'title' => config('app.name', 'Boycott Israeli Tech'), // set false to total remove
            'description' => 'Find ethical alternatives to Israeli tech products and companies', // set false to total remove
            'url' => null, // Set null for using Url::current(), set false to total remove
            'type' => 'website',
            'site_name' => config('app.name', 'Boycott Israeli Tech'),
            'images' => [
                // asset('images/og-image.jpg')
            ],
            'locale' => app()->getLocale(),
        ],
    ],
    'twitter' => [
        /*
         * The default values to be used by the twitter cards generator.
         */
        'defaults' => [
            'title' => config('app.name', 'Boycott Israeli Tech'),
            'description' => 'Find ethical alternatives to Israeli tech products and companies',
            'card' => 'summary_large_image',
            // 'site' => '@yourtwitterhandle', //todo add site stwitter handle
        ],
    ],
    'json-ld' => [
        /*
         * The default configurations to be used by the json-ld generator.
         */
        'defaults' => [
            'title' => config('app.name', 'Boycott Israeli Tech'), // set false to total remove
            'description' => 'Find ethical alternatives to Israeli tech products and companies', // set false to total remove
            'url' => 'current', // Set to null or 'full' to use Url::full(), set to 'current' to use Url::current(), set false to total remove
            'type' => 'WebSite',
            'images' => [
                // asset('images/og-image.jpg')
            ],
        ],
    ],
];
