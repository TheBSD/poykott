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
        $formatDescription = $this->formatAlternativeDescription($alternative);

        $this->seoSetPageAction->execute(
            $alternative->name,
            $formatDescription,
            $alternative->image_path,
            'product'
        );

        // Add structured data for product
        JsonLdMulti::setType('Product');
        JsonLdMulti::addValue('name', $alternative->name);
        JsonLdMulti::addValue('description', $formatDescription);
        JsonLdMulti::addValue('url', $alternative->url);

        // Add tags as keywords
        if ($alternative->tagsRelation?->isNotEmpty()) {
            $keywords = $alternative->tagsRelation->pluck('name')->implode(', ');
            SEOMeta::addKeyword($keywords);
        }
    }

    private function formatAlternativeDescription(Alternative $alternative): string
    {
        if ($alternative->description) {
            return $alternative->description;
        }

        return "{$alternative->name} ({$alternative->slug})";
    }
}
