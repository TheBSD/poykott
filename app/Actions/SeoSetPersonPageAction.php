<?php

namespace App\Actions;

use App\Models\Person;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Illuminate\Support\Str;

class SeoSetPersonPageAction
{
    public function __construct(
        private readonly SeoSetPageAction $seoSetPageAction
    ) {}

    public function execute(Person $person): void
    {
        $fullDescription = $this->formatPersonDescription($person->job_title, $person->description);

        $this->seoSetPageAction->execute(
            $person->name,
            $fullDescription,
            $person->image_path,
            'person'
        );

        // Add structured data for person
        JsonLdMulti::setType('Person');
        JsonLdMulti::addValue('name', $person->name);
        JsonLdMulti::addValue('description', $fullDescription);
        JsonLdMulti::addValue('url', $person->url);
    }

    private function formatPersonDescription($jobTitle, $description): string
    {
        return collect([$jobTitle, $description])
            ->filter()
            ->map(fn ($item) => Str::trim($item))
            ->implode(' - ');
    }
}
