<?php

namespace App\Actions;

use Artesaos\SEOTools\Facades\JsonLdMulti;

class SeoSetFaqsPageAction
{
    public function __construct(
        private readonly SeoSetPageAction $seoSetPageAction
    ) {}

    public function execute($faqs): void
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

        // Add structured data for person
        JsonLdMulti::setType('FAQPage');
        JsonLdMulti::addValue('name', 'FAQs');
        JsonLdMulti::addValue('description', 'Frequently Asked Questions');
        JsonLdMulti::addValue('url', 'faqs');
        JsonLdMulti::addValue('mainEntity', $mainEntity);
    }
}
