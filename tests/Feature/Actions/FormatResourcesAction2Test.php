<?php

use App\Actions\FormatResourcesWikipediaStyleAction;

it('formats wikipedia style references for same domain', function (): void {
    $resources = collect([
        (object) ['url' => 'https://techaviv.com/page1'],
        (object) ['url' => 'https://techaviv.com/page2'],
        (object) ['url' => 'https://techaviv.com/page3'],
    ]);

    $result = (new FormatResourcesWikipediaStyleAction)->execute($resources);

    expect($result)->toBe([
        'https://techaviv.com/page1' => 'techaviv.com [1]',
        'https://techaviv.com/page2' => '[2]',
        'https://techaviv.com/page3' => '[3]',
    ]);
});

it('handles multiple different domains independently', function (): void {
    $resources = collect([
        (object) ['url' => 'https://techaviv.com/page1'],
        (object) ['url' => 'https://jobs.techaviv.com/job'],
        (object) ['url' => 'https://example.com/page'],
        (object) ['url' => 'https://example.com/other'],
    ]);

    $result = (new FormatResourcesWikipediaStyleAction)->execute($resources);

    expect($result)->toBe([
        'https://techaviv.com/page1' => 'techaviv.com [1]',
        'https://jobs.techaviv.com/job' => 'jobs.techaviv.com [1]',
        'https://example.com/page' => 'example.com [1]',
        'https://example.com/other' => '[2]',
    ]);
});
