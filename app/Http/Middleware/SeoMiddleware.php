<?php

namespace App\Http\Middleware;

use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SeoMiddleware
{
    /**
     * Here we set the dynamic meta tags that we cannot set in the config file in seotools.php
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        OpenGraph::addImage(asset('images/og-image.jpg')); // todo change to setImage after accept this pr https://github.com/artesaos/seotools/pull/335

        TwitterCard::setUrl(url()->current());
        TwitterCard::setImage(asset('images/og-image.jpg'));

        /**
         * For JsonLdMulti
         * `setImage` set just one image and remove all previous images
         * `addImage` set multiple images and keep all previous images
         */
        JsonLdMulti::setImage(asset('images/og-image.jpg'));

        /**
         * Supporting Multi langues SEO
         *
         * This is an example code for that
         *
         * $supportedLocales = ['en', 'ar', 'fr'];
         * $currentUrl = url()->current();
         *
         *  foreach ($supportedLocales as $locale) {
         *      $localizedUrl = str_replace('/' . app()->getLocale() . '/', '/' . $locale . '/', $currentUrl);
         *      SEOMeta::addAlternateLanguage($locale, $localizedUrl);
         *   }
         */

        return $next($request);
    }
}
