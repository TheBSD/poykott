<?php

namespace App\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FormatResourcesWikipediaStyleAction
{
    /**
     * Format a collection of resource URLs into a Wikipedia-style reference format.
     *
     * Example Output:
     * [
     *     'https://techaviv.com/page1' => 'techaviv.com [1]',
     *     'https://techaviv.com/page2' => '[2]',
     *     'https://techaviv.com/page3' => '[3]',
     * ]
     */
    public function execute(Collection $resources): array
    {
        $counter = [];

        return $resources->pluck('url')->mapWithKeys(function ($url) use (&$counter) {
            $name = Str::ltrim(parse_url($url, PHP_URL_HOST), 'www.') ?: $url;

            $count = ($counter[$name] ?? 0) + 1;
            $counter[$name] = $count;

            $label = $count === 1
                ? "{$name} [{$count}]"
                : "[{$count}]";

            return [$url => $label];
        })->toArray();
    }
}
