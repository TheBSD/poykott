<?php

use App\Actions\FormatResourcesAction;

it('formats unique domains without suffix', function (): void {
    $resources = collect([
        (object) ['url' => 'https://techaviv.com/page1'],
        (object) ['url' => 'https://jobs.techaviv.com'],
    ]);

    $result = (new FormatResourcesAction)->execute($resources);

    expect($result)->toBe([
        'https://techaviv.com/page1' => 'techaviv.com',
        'https://jobs.techaviv.com' => 'jobs.techaviv.com',
    ]);
});

it('adds numeric suffix for duplicate domains', function (): void {
    $resources = collect([
        (object) ['url' => 'https://techaviv.com/page1'],
        (object) ['url' => 'https://techaviv.com/page2'],
        (object) ['url' => 'https://techaviv.com/page3'],
    ]);

    $result = (new FormatResourcesAction)->execute($resources);

    expect($result)->toBe([
        'https://techaviv.com/page1' => 'techaviv.com',
        'https://techaviv.com/page2' => 'techaviv.com [2]',
        'https://techaviv.com/page3' => 'techaviv.com [3]',
    ]);
});

it('handles www domains by stripping prefix', function (): void {
    $resources = collect([
        (object) ['url' => 'https://www.example.com/page'],
    ]);

    $result = (new FormatResourcesAction)->execute($resources);

    expect($result)->toBe([
        'https://www.example.com/page' => 'example.com',
    ]);
});
