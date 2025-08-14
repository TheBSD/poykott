<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Alternative;
use App\Models\User;
use App\Policies\AlternativePolicy;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class AlternativePolicyTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private AlternativePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new AlternativePolicy();
    }

    private function mockUserExpectingCan(string $expectedPermission, bool $returns): User
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')
            ->once()
            ->with($expectedPermission)
            ->andReturn($returns);
        return $user;
    }

    private function alternative(): Alternative
    {
        // The Alternative instance is not used by the policy logic, only type-hinted.
        // Provide a simple instance; if Alternative requires attributes in your app,
        // you can switch this to a Mockery mock instead.
        return new Alternative();
    }

    public function test_viewAny_allows_when_user_has_permission(): void
    {
        $user = $this->mockUserExpectingCan('view_any_alternative', true);
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_viewAny_denies_when_user_lacks_permission(): void
    {
        $user = $this->mockUserExpectingCan('view_any_alternative', false);
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_allows_when_user_has_permission(): void
    {
        $user = $this->mockUserExpectingCan('view_alternative', true);
        $this->assertTrue($this->policy->view($user, $this->alternative()));
    }

    public function test_view_denies_when_user_lacks_permission(): void
    {
        $user = $this->mockUserExpectingCan('view_alternative', false);
        $this->assertFalse($this->policy->view($user, $this->alternative()));
    }

    public function test_create_allows_when_user_has_permission(): void
    {
        $user = $this->mockUserExpectingCan('create_alternative', true);
        $this->assertTrue($this->policy->create($user));
    }

    public function test_create_denies_when_user_lacks_permission(): void
    {
        $user = $this->mockUserExpectingCan('create_alternative', false);
        $this->assertFalse($this->policy->create($user));
    }

    public function test_update_allows_when_user_has_permission(): void
    {
        $user = $this->mockUserExpectingCan('update_alternative', true);
        $this->assertTrue($this->policy->update($user, $this->alternative()));
    }

    public function test_update_denies_when_user_lacks_permission(): void
    {
        $user = $this->mockUserExpectingCan('update_alternative', false);
        $this->assertFalse($this->policy->update($user, $this->alternative()));
    }

    public function test_delete_allows_when_user_has_permission(): void
    {
        $user = $this->mockUserExpectingCan('delete_alternative', true);
        $this->assertTrue($this->policy->delete($user, $this->alternative()));
    }

    public function test_delete_denies_when_user_lacks_permission(): void
    {
        $user = $this->mockUserExpectingCan('delete_alternative', false);
        $this->assertFalse($this->policy->delete($user, $this->alternative()));
    }

    public function test_deleteAny_allows_when_user_has_permission(): void
    {
        $user = $this->mockUserExpectingCan('delete_any_alternative', true);
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_deleteAny_denies_when_user_lacks_permission(): void
    {
        $user = $this->mockUserExpectingCan('delete_any_alternative', false);
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_forceDelete_allows_when_user_has_permission(): void
    {
        $user = $this->mockUserExpectingCan('force_delete_alternative', true);
        $this->assertTrue($this->policy->forceDelete($user, $this->alternative()));
    }

    public function test_forceDelete_denies_when_user_lacks_permission(): void
    {
        $user = $this->mockUserExpectingCan('force_delete_alternative', false);
        $this->assertFalse($this->policy->forceDelete($user, $this->alternative()));
    }

    public function test_forceDeleteAny_allows_when_user_has_permission(): void
    {
        $user = $this->mockUserExpectingCan('force_delete_any_alternative', true);
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_forceDeleteAny_denies_when_user_lacks_permission(): void
    {
        $user = $this->mockUserExpectingCan('force_delete_any_alternative', false);
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_restore_allows_when_user_has_permission(): void
    {
        $user = $this->mockUserExpectingCan('restore_alternative', true);
        $this->assertTrue($this->policy->restore($user, $this->alternative()));
    }

    public function test_restore_denies_when_user_lacks_permission(): void
    {
        $user = $this->mockUserExpectingCan('restore_alternative', false);
        $this->assertFalse($this->policy->restore($user, $this->alternative()));
    }

    public function test_restoreAny_allows_when_user_has_permission(): void
    {
        $user = $this->mockUserExpectingCan('restore_any_alternative', true);
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_restoreAny_denies_when_user_lacks_permission(): void
    {
        $user = $this->mockUserExpectingCan('restore_any_alternative', false);
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_replicate_allows_when_user_has_permission(): void
    {
        $user = $this->mockUserExpectingCan('replicate_alternative', true);
        $this->assertTrue($this->policy->replicate($user, $this->alternative()));
    }

    public function test_replicate_denies_when_user_lacks_permission(): void
    {
        $user = $this->mockUserExpectingCan('replicate_alternative', false);
        $this->assertFalse($this->policy->replicate($user, $this->alternative()));
    }

    public function test_reorder_allows_when_user_has_permission(): void
    {
        $user = $this->mockUserExpectingCan('reorder_alternative', true);
        $this->assertTrue($this->policy->reorder($user));
    }

    public function test_reorder_denies_when_user_lacks_permission(): void
    {
        $user = $this->mockUserExpectingCan('reorder_alternative', false);
        $this->assertFalse($this->policy->reorder($user));
    }
}