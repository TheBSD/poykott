<?php

namespace App\Actions;

use Artesaos\SEOTools\Facades\JsonLdMulti;
use Illuminate\Database\Eloquent\Collection;

class SeoSetFaqsPageAction
{
    public function __construct(
        private readonly SeoSetPageAction $seoSetPageAction
    ) {}

    public function execute(Collection $faqs): void
    {
        $this->seoSetPageAction->execute(
            'FAQs',
            'Frequently Asked Questions',
        );

        $mainEntity = [];

        foreach ($faqs as $faq) {
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq->answer,
                ],
            ];
        }

        JsonLdMulti::setType('FAQPage');
        JsonLdMulti::addValue('name', 'FAQs');
        JsonLdMulti::addValue('description', 'Frequently Asked Questions');
        JsonLdMulti::addValue('url', route('faqs'));
        JsonLdMulti::addValue('mainEntity', $mainEntity);
    }
}
