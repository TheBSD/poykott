<?php

use App\Models\ContactMessage;

describe('Contact Form Validation', function (): void {
    it('requires name field', function (): void {
        $response = $this->post(route('contact.store'), [
            'email' => 'test@example.com',
            'message' => 'This is a test message that is long enough.',
        ]);

        $response->assertSessionHasErrors('name');
    });

    it('requires email field', function (): void {
        $response = $this->post(route('contact.store'), [
            'name' => 'Test User',
            'message' => 'This is a test message that is long enough.',
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('requires valid email format', function (): void {
        $response = $this->post(route('contact.store'), [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'message' => 'This is a test message that is long enough.',
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('requires message field', function (): void {
        $response = $this->post(route('contact.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('message');
    });

    it('requires message to be at least 10 characters', function (): void {
        $response = $this->post(route('contact.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Short',
        ]);

        $response->assertSessionHasErrors('message');
    });

    it('limits message to 10000 characters', function (): void {
        $longMessage = str_repeat('a', 10001);

        $response = $this->post(route('contact.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => $longMessage,
        ]);

        $response->assertSessionHasErrors('message');
    });

    it('limits name to 255 characters', function (): void {
        $longName = str_repeat('a', 256);

        $response = $this->post(route('contact.store'), [
            'name' => $longName,
            'email' => 'test@example.com',
            'message' => 'This is a test message that is long enough.',
        ]);

        $response->assertSessionHasErrors('name');
    });
});

describe('Contact Form Submission', function (): void {
    it('successfully creates contact message with valid data', function (): void {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'This is a test message that is long enough to pass validation.',
        ];

        $response = $this->post(route('contact.store'), $data);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Message sent successfully');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'This is a test message that is long enough to pass validation.',
            'ip_address' => '127.0.0.1',
        ]);
    });

    it('captures IP address when creating contact message', function (): void {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'This is a test message that is long enough to pass validation.',
        ];

        $response = $this->from('http://example.com')
            ->withServerVariables(['REMOTE_ADDR' => '192.168.1.100'])
            ->post(route('contact.store'), $data);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Message sent successfully');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'ip_address' => '192.168.1.100',
        ]);
    });
});

describe('Spam Protection', function (): void {
    it('blocks submission from spam email address', function (): void {
        // First, create a spam message
        ContactMessage::factory()->create([
            'email' => 'spam@example.com',
            'spam_at' => now(),
        ]);

        $data = [
            'name' => 'Test User',
            'email' => 'spam@example.com',
            'message' => 'This should be blocked because email is marked as spam.',
        ];

        $response = $this->post(route('contact.store'), $data);

        $response->assertRedirect()
            ->assertSessionHas('error', 'Unable to send message. Please try again later.');

        // Verify no new message was created
        $this->assertEquals(1, ContactMessage::query()->where('email', 'spam@example.com')->count());
    });

    it('blocks submission from spam IP address', function (): void {
        // First, create a spam message with IP
        ContactMessage::factory()->create([
            'ip_address' => '192.168.1.100',
            'spam_at' => now(),
        ]);

        $data = [
            'name' => 'Test User',
            'email' => 'different@example.com',
            'message' => 'This should be blocked because IP is marked as spam.',
        ];

        $response = $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.100'])
            ->post(route('contact.store'), $data);

        $response->assertRedirect()
            ->assertSessionHas('error', 'Unable to send message. Please try again later.');

        // Verify no new message was created for this email
        $this->assertEquals(0, ContactMessage::query()->where('email', 'different@example.com')->count());
    });

    it('allows submission from non-spam email and IP', function (): void {
        // Create a spam message with different email and IP
        ContactMessage::factory()->create([
            'email' => 'spam@example.com',
            'ip_address' => '192.168.1.100',
            'spam_at' => now(),
        ]);

        $data = [
            'name' => 'Test User',
            'email' => 'legitimate@example.com',
            'message' => 'This should be allowed because email and IP are not spam.',
        ];

        $response = $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.200'])
            ->post(route('contact.store'), $data);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Message sent successfully');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'legitimate@example.com',
            'ip_address' => '192.168.1.200',
        ]);
    });

    it('allows submission from unmarked spam email', function (): void {
        // Create a message that was spam but is no longer marked as spam
        ContactMessage::factory()->create([
            'email' => 'reformed@example.com',
            'spam_at' => null, // Not spam anymore
        ]);

        $data = [
            'name' => 'Test User',
            'email' => 'reformed@example.com',
            'message' => 'This should be allowed because email is no longer marked as spam.',
        ];

        $response = $this->post(route('contact.store'), $data);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Message sent successfully');

        $this->assertEquals(2, ContactMessage::query()->where('email', 'reformed@example.com')->count());
    });
});
