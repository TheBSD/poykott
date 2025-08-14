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

<?php

test('normalize URLs with ports, credentials, fragments, and complex paths', function (): void {
    $cases = [
        // ports (preserve)
        'example.com:8080' => 'https://example.com:8080/',
        'http://example.com:8080' => 'http://example.com:8080/',
        'https://example.com:443' => 'https://example.com:443/',
        // credentials (preserve)
        'user:pass@example.com' => 'https://user:pass@example.com/',
        'http://user:pass@example.com' => 'http://user:pass@example.com/',
        // fragments (ensure slash before fragment, preserve fragment)
        'https://example.com#section' => 'https://example.com/#section',
        'https://example.com/path#frag' => 'https://example.com/path/#frag',
        // query + fragment (ensure slash before ?, preserve both)
        'https://example.com?foo=bar#frag' => 'https://example.com/?foo=bar#frag',
        'https://example.com/path?x=1#y' => 'https://example.com/path/?x=1#y',
        // multiple segments and redundant slashes (collapse to single, keep trailing)
        'https://example.com//a///b' => 'https://example.com/a/b/',
        'example.com///a//b///c' => 'https://example.com/a/b/c/',
        // trailing slash always applied to non-empty path
        'example.com/a' => 'https://example.com/a/',
        'http://example.com/a/b' => 'http://example.com/a/b/',
    ];

    foreach ($cases as $input => $expected) {
        expect(normalizedUrlSchemasAndTrailingSlashes($input))->toBe($expected);
    }
});

test('normalize URLs with whitespace, tabs, and newlines are trimmed', function (): void {
    $cases = [
        " \n\thttps://example.com/path\t " => 'https://example.com/path/',
        "  example.com  " => 'https://example.com/',
        "\twww.example.com\n" => 'https://www.example.com/',
    ];

    foreach ($cases as $input => $expected) {
        expect(normalizedUrlSchemasAndTrailingSlashes($input))->toBe($expected);
    }
});

test('normalize non-http schemes are preserved (do not force https for others)', function (): void {
    $cases = [
        // mailto and ftp should stay as-is (no trailing slash typically appended)
        'mailto:user@example.com' => 'mailto:user@example.com',
        'ftp://example.com/resource' => 'ftp://example.com/resource',
        // data URLs should remain unchanged
        'data:text/plain,Hello%20World' => 'data:text/plain,Hello%20World',
    ];

    foreach ($cases as $input => $expected) {
        expect(normalizedUrlSchemasAndTrailingSlashes($input))->toBe($expected);
    }
});

test('normalize handles IPs, IDN/punycode, and bracketed hosts', function (): void {
    $cases = [
        // IPv4
        '192.168.1.10' => 'https://192.168.1.10/',
        'http://127.0.0.1' => 'http://127.0.0.1/',
        'http://127.0.0.1/test' => 'http://127.0.0.1/test/',
        // Bracketed IPv6-like hostnames (common URL form)
        '[2001:db8::1]' => 'https://[2001:db8::1]/',
        'http://[2001:db8::1]/a' => 'http://[2001:db8::1]/a/',
        // IDN: If input is unicode, expect normalized output to keep it or to punycode depending on implementation.
        // We assert that trailing slash and scheme defaulting still apply regardless.
        'ドメイン例.テスト' => 'https://ドメイン例.テスト/',
        'www.xn--eckwd4c7c.xn--zckzah' => 'https://www.xn--eckwd4c7c.xn--zckzah/',
    ];

    foreach ($cases as $input => $expected) {
        expect(normalizedUrlSchemasAndTrailingSlashes($input))->toBe($expected);
    }
});

test('normalize mixed-case scheme and host without forcing lowercase path', function (): void {
    // We do not assert forced lowercase; domain names are case-insensitive but paths are case-sensitive.
    // The main invariant we verify: scheme is preserved and trailing slash behavior is correct.
    $cases = [
        'HTTPS://Example.COM/CaseSensitive/Path' => 'https://Example.COM/CaseSensitive/Path/',
        'HTTP://Example.COM/ABC?Q=1' => 'http://Example.COM/ABC/?Q=1',
    ];

    foreach ($cases as $input => $expected) {
        expect(normalizedUrlSchemasAndTrailingSlashes($input))->toBe($expected);
    }
});

test('normalize gracefully handles empty or whitespace-only inputs', function (): void {
    // Depending on implementation, empty may return empty string or possibly "https:///".
    // We assert that empty remains empty and whitespace-only becomes empty after trimming.
    $cases = [
        '' => '',
        '   ' => '',
        "\n\t " => '',
    ];

    foreach ($cases as $input => $expected) {
        expect(normalizedUrlSchemasAndTrailingSlashes($input))->toBe($expected);
    }
});

test('normalize does not add trailing slash to file-like paths with extensions if implementation preserves extension semantics', function (): void {
    // If the implementation always adds a trailing slash regardless of file-like endings,
    // update these expectations accordingly. Here we assume it preserves file semantics
    // by adding a slash only when path is a directory-like segment.
    $cases = [
        'https://example.com/index.html' => 'https://example.com/index.html',
        'example.com/image.png' => 'https://example.com/image.png',
        'http://example.com/archive.tar.gz?dl=1' => 'http://example.com/archive.tar.gz?dl=1',
    ];

    foreach ($cases as $input => $expected) {
        expect(normalizedUrlSchemasAndTrailingSlashes($input))->toBe($expected);
    }
});
