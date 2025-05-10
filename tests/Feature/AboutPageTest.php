<?php

test('about page', function (): void {
    $this->get('about')
        ->assertOk()
        ->assertViewIs('pages.about')
        ->assertSeeText('About Us');
});
