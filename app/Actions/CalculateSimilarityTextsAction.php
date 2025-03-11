<?php

namespace App\Actions;

use Illuminate\Support\Str;

class CalculateSimilarityTextsAction
{
    /**
     * Calculate Similarity between two texts.
     */
    public function execute(string $first, string $second): int
    {
        $first = $this->prepareText($first);
        $second = $this->prepareText($second);

        similar_text($first, $second, $percent);

        return intval(round($percent));
    }

    /**
     * Prepare the input text by removing non-alphanumeric characters and extra spaces.
     */
    private function prepareText(string $text): string
    {
        return Str::of(preg_replace('/[^a-zA-Z0-9 ]/', '', $text))
            ->squish()
            ->value();
    }
}
