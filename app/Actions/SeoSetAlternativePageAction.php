<?php

namespace App\Actions;

use App\Models\Alternative;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\SEOMeta;

class SeoSetAlternativePageAction
{
    public function __construct(
        private readonly SeoSetPageAction $seoSetPageAction
    ) {}

    public function execute(Alternative $alternative): void
    {
        $this->seoSetPageAction->execute(
            $alternative->name,
            $alternative->description,
            $alternative->image_path,
            'product'
        );

        // Add structured data for product
        JsonLdMulti::setType('Product');
        JsonLdMulti::addValue('name', $alternative->name);
        JsonLdMulti::addValue('description', $alternative->description);
        JsonLdMulti::addValue('url', $alternative->url);

        // Add tags as keywords
        if ($alternative->tagsRelation?->isNotEmpty()) {
            $keywords = $alternative->tagsRelation->pluck('name')->implode(', ');
            SEOMeta::addKeyword($keywords);
        }
    }
}
