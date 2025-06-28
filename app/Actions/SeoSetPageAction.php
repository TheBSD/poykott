<?php

namespace App\Actions;

use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;

class SeoSetPageAction
{
    public function execute(string $title, $description, $image = null, $type = 'website'): void
    {
        $fullTitle = $title . ' - ' . config('app.name', 'Boycott Israeli Tech');

        SEOMeta::setTitle($title);
        SEOMeta::setDescription($description);

        OpenGraph::setTitle($fullTitle);
        OpenGraph::setDescription($description);
        OpenGraph::setType($type);

        TwitterCard::setTitle($fullTitle);
        TwitterCard::setDescription($description);

        JsonLdMulti::setTitle($fullTitle);
        JsonLdMulti::setDescription($description);
        JsonLdMulti::setType($type);

        if ($image) {
            OpenGraph::addImage($image); // todo change to setImage after accept this pr https://github.com/artesaos/seotools/pull/335
            TwitterCard::setImage($image);
            JsonLdMulti::setImage($image);
        }
    }
}
