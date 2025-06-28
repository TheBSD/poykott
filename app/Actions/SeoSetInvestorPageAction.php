<?php

namespace App\Actions;

use App\Models\Investor;
use Artesaos\SEOTools\Facades\JsonLdMulti;

class SeoSetInvestorPageAction
{
    public function __construct(
        private readonly SeoSetPageAction $seoSetPageAction
    ) {}

    public function execute(Investor $investor): void
    {
        $fullDescription = $this->formatInvestorDescription($investor);

        $this->seoSetPageAction->execute(
            $investor->name,
            $fullDescription,
            $investor->image_path,
            'company'
        );

        // Add structured data for person
        JsonLdMulti::setType('Company');
        JsonLdMulti::addValue('name', $investor->name);
        JsonLdMulti::addValue('description', $fullDescription);
        JsonLdMulti::addValue('url', $investor->url);
    }

    private function formatInvestorDescription(Investor $investor)
    {
        if ($investor->description) {
            return $investor->description;
        }

        $investorCompanies = $investor
            ->companies
            ->filter(fn ($company) => $company->approved_at)
            ->pluck('name')
            ->toArray();

        return $investor->name . ' is an investor company that invest in Israeli companies:' . implode(', ', $investorCompanies);
    }
}
