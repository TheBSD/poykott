<?php

test('normalize various URLs correctly', function (): void {
    $cases = [
        'example.com' => 'https://example.com/',
        'http://example.com' => 'http://example.com/',
        'https://example.com' => 'https://example.com/',
        'https://example.com/' => 'https://example.com/',
        'example.com/test' => 'https://example.com/test/',
        'http://example.com/test' => 'http://example.com/test/',
        'https://example.com/test/' => 'https://example.com/test/',
        ' https://example.com/test ' => 'https://example.com/test/',
        'www.example.com' => 'https://www.example.com/',
        'https://example.com?foo=bar' => 'https://example.com/?foo=bar',
        'https://example.com/path?x=1' => 'https://example.com/path/?x=1',
    ];

    foreach ($cases as $input => $expected) {
        expect(normalizedUrlSchemasAndTrailingSlashes($input))->toBe($expected);
    }
});
