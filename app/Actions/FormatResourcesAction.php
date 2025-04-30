<?php

namespace App\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FormatResourcesAction
{
    /**
     * Format a collection of resource URLs into a unique, readable format.
     *
     * If multiple URLs share the same domain (e.g., "techaviv.com"), a numeric suffix
     * is added to distinguish them: "techaviv.com", "techaviv.com [2]", etc.
     *
     * Example Output:
     * [
     *     'https://techaviv.com/page1' => 'techaviv.com',
     *     'https://techaviv.com/page2' => 'techaviv.com [2]',
     *     'https://jobs.techaviv.com' => 'jobs.techaviv.com',
     * ]
     *
     * @param  Collection  $resources  A collection of resources, each with a 'url' property
     * @return array<string, string> An array with original URLs as keys and formatted labels as values
     */
    public function execute(Collection $resources): array
    {
        // Track how many times each domain (or display name) appears
        $counter = [];

        return $resources->pluck('url')->mapWithKeys(function ($url) use (&$counter) {
            // Extract domain name (e.g., 'techaviv.com') without 'www.'
            $name = Str::ltrim(parse_url($url, PHP_URL_HOST), 'www.') ?: $url;

            // Increment counter for this domain
            $count = ($counter[$name] ?? 0) + 1;
            $counter[$name] = $count;

            // If seen before, append count to make label unique
            $label = $count > 1 ? "{$name} [{$count}]" : $name;

            // Return the original URL as key and label as value
            return [$url => $label];
        })->toArray();
    }
}
