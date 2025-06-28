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

        JsonLdMulti::setType('Company');
        JsonLdMulti::addValue('name', $investor->name);
        JsonLdMulti::addValue('description', $fullDescription);
        JsonLdMulti::addValue('url', $investor->url);
    }

    private function formatInvestorDescription(Investor $investor): string
    {
        if ($investor->description) {
            return $investor->description;
        }

        $investor->loadMissing('companies'); // ensure relationships are loaded

        $investorCompanies = $investor
            ->companies
            ->filter(fn ($company) => $company->approved_at)
            ->pluck('name')
            ->toArray();

        return $investor->name . ' is an investor company that invests in Israeli companies: ' . implode(', ', $investorCompanies);
    }
}
