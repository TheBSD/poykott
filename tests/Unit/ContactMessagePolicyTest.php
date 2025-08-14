<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\ContactMessage;
use App\Models\User;
use App\Policies\ContactMessagePolicy;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ContactMessagePolicyTest extends TestCase
{
    /** @var ContactMessagePolicy */
    private $policy;

    /** @var ContactMessage */
    private $message;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ContactMessagePolicy();
        // The policy does not use any fields on ContactMessage; a simple instance suffices.
        $this->message = new ContactMessage();
    }

    /**
     * Helper to build a User mock that grants only the provided ability string.
     *
     * @param string $allowedAbility
     * @return User&MockObject
     */
    private function userAllowingOnly(string $allowedAbility): User
    {
        /** @var User&MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('can')->willReturnCallback(static function (string $ability) use ($allowedAbility): bool {
            return $ability === $allowedAbility;
        });
        return $user;
    }

    /**
     * Helper to build a User mock that denies everything.
     *
     * @return User&MockObject
     */
    private function userDenyingAll(): User
    {
        /** @var User&MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('can')->willReturn(false);
        return $user;
    }

    public function test_viewAny_allows_when_user_has_view_any_permission(): void
    {
        $user = $this->userAllowingOnly('view_any_contact::message');
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_viewAny_denies_when_user_lacks_view_any_permission(): void
    {
        $user = $this->userDenyingAll();
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_allows_when_user_has_view_permission(): void
    {
        $user = $this->userAllowingOnly('view_contact::message');
        $this->assertTrue($this->policy->view($user, $this->message));
    }

    public function test_view_denies_when_user_lacks_view_permission(): void
    {
        $user = $this->userDenyingAll();
        $this->assertFalse($this->policy->view($user, $this->message));
    }

    public function test_create_allows_when_user_has_create_permission(): void
    {
        $user = $this->userAllowingOnly('create_contact::message');
        $this->assertTrue($this->policy->create($user));
    }

    public function test_create_denies_when_user_lacks_create_permission(): void
    {
        $user = $this->userDenyingAll();
        $this->assertFalse($this->policy->create($user));
    }

    public function test_update_allows_when_user_has_update_permission(): void
    {
        $user = $this->userAllowingOnly('update_contact::message');
        $this->assertTrue($this->policy->update($user, $this->message));
    }

    public function test_update_denies_when_user_lacks_update_permission(): void
    {
        $user = $this->userDenyingAll();
        $this->assertFalse($this->policy->update($user, $this->message));
    }

    public function test_delete_allows_when_user_has_delete_permission(): void
    {
        $user = $this->userAllowingOnly('delete_contact::message');
        $this->assertTrue($this->policy->delete($user, $this->message));
    }

    public function test_delete_denies_when_user_lacks_delete_permission(): void
    {
        $user = $this->userDenyingAll();
        $this->assertFalse($this->policy->delete($user, $this->message));
    }

    public function test_deleteAny_allows_when_user_has_delete_any_permission(): void
    {
        $user = $this->userAllowingOnly('delete_any_contact::message');
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_deleteAny_denies_when_user_lacks_delete_any_permission(): void
    {
        $user = $this->userDenyingAll();
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_forceDelete_allows_when_user_has_force_delete_permission(): void
    {
        $user = $this->userAllowingOnly('force_delete_contact::message');
        $this->assertTrue($this->policy->forceDelete($user, $this->message));
    }

    public function test_forceDelete_denies_when_user_lacks_force_delete_permission(): void
    {
        $user = $this->userDenyingAll();
        $this->assertFalse($this->policy->forceDelete($user, $this->message));
    }

    public function test_forceDeleteAny_allows_when_user_has_force_delete_any_permission(): void
    {
        $user = $this->userAllowingOnly('force_delete_any_contact::message');
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_forceDeleteAny_denies_when_user_lacks_force_delete_any_permission(): void
    {
        $user = $this->userDenyingAll();
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_restore_allows_when_user_has_restore_permission(): void
    {
        $user = $this->userAllowingOnly('restore_contact::message');
        $this->assertTrue($this->policy->restore($user, $this->message));
    }

    public function test_restore_denies_when_user_lacks_restore_permission(): void
    {
        $user = $this->userDenyingAll();
        $this->assertFalse($this->policy->restore($user, $this->message));
    }

    public function test_restoreAny_allows_when_user_has_restore_any_permission(): void
    {
        $user = $this->userAllowingOnly('restore_any_contact::message');
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_restoreAny_denies_when_user_lacks_restore_any_permission(): void
    {
        $user = $this->userDenyingAll();
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_replicate_allows_when_user_has_replicate_permission(): void
    {
        $user = $this->userAllowingOnly('replicate_contact::message');
        $this->assertTrue($this->policy->replicate($user, $this->message));
    }

    public function test_replicate_denies_when_user_lacks_replicate_permission(): void
    {
        $user = $this->userDenyingAll();
        $this->assertFalse($this->policy->replicate($user, $this->message));
    }

    public function test_reorder_allows_when_user_has_reorder_permission(): void
    {
        $user = $this->userAllowingOnly('reorder_contact::message');
        $this->assertTrue($this->policy->reorder($user));
    }

    public function test_reorder_denies_when_user_lacks_reorder_permission(): void
    {
        $user = $this->userDenyingAll();
        $this->assertFalse($this->policy->reorder($user));
    }

    /**
     * Defensive test: if the policy accidentally changes an ability string,
     * our callback-based mocks will cause an unexpected false result here.
     * This ensures exact string matching is enforced.
     */
    public function test_exact_permission_strings_are_required(): void
    {
        // For example, if a stray space or typo were introduced, this would fail.
        $expected = 'view_any_contact::message';
        $user = $this->userAllowingOnly($expected);

        // Sanity: should be true with exact match
        $this->assertTrue($this->policy->viewAny($user));

        // Now simulate calling "view" which expects a different permission
        // and should therefore be denied by this mock.
        $this->assertFalse($this->policy->view($user, $this->message));
    }
}