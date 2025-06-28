<?php

namespace App\Actions;

use App\Models\Company;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\SEOMeta;

class SeoSetCompanyPageAction
{
    public function __construct(
        private readonly SeoSetPageAction $seoSetPageAction
    ) {}

    public function execute(Company $company): void
    {
        $formatDescription = $this->formatCompanyDescription($company);

        $this->seoSetPageAction->execute(
            $company->name,
            $formatDescription,
            $company->image_path,
            'product'
        );

        // Add structured data for product
        JsonLdMulti::setType('Product');
        JsonLdMulti::addValue('name', $company->name);
        JsonLdMulti::addValue('description', $formatDescription);
        JsonLdMulti::addValue('url', $company->url);

        // Add tags as keywords
        if ($company->tagsRelation?->isNotEmpty()) {
            $keywords = $company->tagsRelation->pluck('name')->implode(', ');
            SEOMeta::addKeyword($keywords);
        }
    }

    private function formatCompanyDescription(Company $company): string
    {
        if ($company->description) {
            return $company->description;
        }

        return "{$company->name} ({$company->slug})";
    }
}
