<?php

namespace Tests\Unit;

use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // Helper to make a Panel stub (the method under test ignores Panel argument)
    protected function makePanelStub(): Panel
    {
        return $this->getMockBuilder(Panel::class)->disableOriginalConstructor()->getMock();
    }

    public function test_scope_is_admin_returns_only_verified_admin_domain_users(): void
    {
        // Non-admin domain, verified
        User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => now(),
        ]);

        // Admin domain, not verified
        User::factory()->create([
            'email' => 'jane@admin.com',
            'email_verified_at' => null,
        ]);

        // Admin domain, verified - should match
        $match = User::factory()->create([
            'email' => 'root@admin.com',
            'email_verified_at' => now(),
        ]);

        // Another admin, verified
        $match2 = User::factory()->create([
            'email' => 'boss@admin.com',
            'email_verified_at' => now(),
        ]);

        $ids = User::query()->isAdmin()->pluck('id')->all();

        $this->assertContains($match->id, $ids, 'Expected verified admin user to be included.');
        $this->assertContains($match2->id, $ids, 'Expected verified admin user to be included.');
        $this->assertCount(2, $ids, 'Only verified @admin.com users should match.');
    }

    public function test_can_access_panel_true_when_admin_domain_and_verified(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
        ]);

        $this->assertTrue($user->canAccessPanel($this->makePanelStub()));
    }

    public function test_can_access_panel_false_when_admin_domain_but_not_verified(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@admin.com',
            'email_verified_at' => null,
        ]);

        $this->assertFalse($user->canAccessPanel($this->makePanelStub()));
    }

    public function test_can_access_panel_false_when_not_admin_domain_even_if_verified(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'email_verified_at' => now(),
        ]);

        $this->assertFalse($user->canAccessPanel($this->makePanelStub()));
    }

    public function test_email_verified_at_casts_to_carbon_instance(): void
    {
        $user = new User();
        $when = '2025-01-02 03:04:05';
        $user->email_verified_at = $when;

        $this->assertInstanceOf(Carbon::class, $user->email_verified_at);
        $this->assertSame('2025-01-02 03:04:05', $user->email_verified_at->format('Y-m-d H:i:s'));
    }

    public function test_password_is_hashed_via_casts(): void
    {
        $plain = 'secret-password-123';
        $user = User::factory()->create([
            'password' => $plain,
        ]);

        $this->assertNotSame($plain, $user->password, 'Password should not be stored in plain text.');
        $this->assertTrue(Hash::check($plain, $user->password), 'Stored hash should verify with original password.');
    }

    public function test_mass_assignment_respects_fillable_and_ignores_unfillable(): void
    {
        $user = User::create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'password123',
            'email_verified_at' => now(),
            // Attempt to set a hidden/unfillable attribute
            'remember_token' => 'should-not-be-mass-assigned',
        ]);

        $this->assertSame('Alice', $user->name);
        $this->assertSame('alice@example.com', $user->email);
        $this->assertTrue(Hash::check('password123', $user->password));

        // Ensure remember_token was not mass-assigned
        $this->assertNull($user->getAttribute('remember_token'));
    }

    public function test_hidden_attributes_do_not_appear_in_array_or_json(): void
    {
        $plain = 'my-plain-pass';
        $user = User::factory()->create([
            'password' => $plain,
            'remember_token' => 'some-remember-token',
        ]);

        $asArray = $user->toArray();
        $this->assertArrayNotHasKey('password', $asArray);
        $this->assertArrayNotHasKey('remember_token', $asArray);

        $asJson = $user->toJson();
        $this->assertStringNotContainsString('password', $asJson);
        $this->assertStringNotContainsString('remember_token', $asJson);
    }

    public function test_scope_is_admin_is_chainable_and_respects_other_conditions(): void
    {
        // Create verified admin and unverified admin
        $verifiedAdmin = User::factory()->create([
            'email' => 'chain@admin.com',
            'email_verified_at' => now(),
            'name' => 'Chain Admin',
        ]);

        User::factory()->create([
            'email' => 'chain2@admin.com',
            'email_verified_at' => null,
            'name' => 'Unverified Admin',
        ]);

        $names = User::query()
            ->isAdmin()
            ->where('name', 'like', '%Chain%')
            ->pluck('name')
            ->all();

        $this->assertSame(['Chain Admin'], $names);
        $this->assertNotContains('Unverified Admin', $names);
    }
}

    public function test_scope_is_admin_excludes_non_admin_non_verified_combinations(): void
    {
        \App\Models\User::factory()->create([
            'email' => 'foo@example.com',
            'email_verified_at' => null,
        ]);
        \App\Models\User::factory()->create([
            'email' => 'bar@example.com',
            'email_verified_at' => now(),
        ]);
        \App\Models\User::factory()->create([
            'email' => 'baz@admin.com',
            'email_verified_at' => null,
        ]);

        $this->assertCount(0, \App\Models\User::query()->isAdmin()->get(), 'No users should match isAdmin in this setup.');
    }

    public function test_can_access_panel_edge_cases_with_uppercase_domain_and_whitespace(): void
    {
        $panel = $this->getMockBuilder(\Filament\Panel::class)->disableOriginalConstructor()->getMock();

        $user = \App\Models\User::factory()->create([
            'email' => "ADMIN@ADMIN.COM",
            'email_verified_at' => now(),
        ]);

        // str_ends_with is case-sensitive; uppercase domain should fail.
        $this->assertFalse($user->canAccessPanel($panel), 'Uppercase domain does not strictly end with @admin.com');

        $user2 = \App\Models\User::factory()->create([
            'email' => "space@admin.com ",
            'email_verified_at' => now(),
        ]);

        // Trailing space should cause failure since ends_with won't trim.
        $this->assertFalse($user2->canAccessPanel($panel));
    }