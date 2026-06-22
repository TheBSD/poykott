<?php

it('adds browser security headers to web responses', function (): void {
    $this->get('/')
        ->assertOk()
        ->assertHeader('Content-Security-Policy')
        ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin-allow-popups')
        ->assertHeader('Cross-Origin-Resource-Policy', 'same-site')
        ->assertHeader('Permissions-Policy')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
});

it('publishes a security contact', function (): void {
    $this->get('/.well-known/security.txt')
        ->assertOk()
        ->assertSee('Contact: https://github.com/TheBSD/poykott/issues', false)
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
});
