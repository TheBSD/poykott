<?php
/**
 * Tests for App\Policies\AuditPolicy.
 * Testing framework: PHPUnit (Laravel default).
 * Mocking library: Mockery (via Laravel's testing integration).
 *
 * Strategy:
 * - For each policy method, assert that it calls User::can() with the correct permission string
 *   and returns the exact boolean result from that call.
 * - Provide both "allows" (true) and "denies" (false) scenarios.
 * - Mock OwenIt\Auditing\Models\Audit where required (it's not used by the logic).
 */

namespace Tests\Unit;

use App\Policies\AuditPolicy;
use App\Models\User;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use OwenIt\Auditing\Models\Audit;
use PHPUnit\Framework\TestCase;

class AuditPolicyTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    // Utility to create a user mock configured to respond to can() with expected permission.
    private function makeUserMockExpecting(string $permission, bool $result)
    {
        $user = m::mock(User::class);
        $user->shouldReceive('can')
            ->once()
            ->with($permission)
            ->andReturn($result);
        return $user;
    }

    // Create an audit mock (not used inside policy methods but required by signature)
    private function makeAuditMock()
    {
        return m::mock(Audit::class);
    }

    public function test_viewAny_allows_when_user_has_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('view_any_audit', true);

        $this->assertTrue($policy->viewAny($user));
    }

    public function test_viewAny_denies_when_user_lacks_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('view_any_audit', false);

        $this->assertFalse($policy->viewAny($user));
    }

    public function test_view_allows_when_user_has_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('view_audit', true);
        $audit = $this->makeAuditMock();

        $this->assertTrue($policy->view($user, $audit));
    }

    public function test_view_denies_when_user_lacks_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('view_audit', false);
        $audit = $this->makeAuditMock();

        $this->assertFalse($policy->view($user, $audit));
    }

    public function test_create_allows_when_user_has_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('create_audit', true);

        $this->assertTrue($policy->create($user));
    }

    public function test_create_denies_when_user_lacks_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('create_audit', false);

        $this->assertFalse($policy->create($user));
    }

    public function test_update_allows_when_user_has_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('update_audit', true);
        $audit = $this->makeAuditMock();

        $this->assertTrue($policy->update($user, $audit));
    }

    public function test_update_denies_when_user_lacks_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('update_audit', false);
        $audit = $this->makeAuditMock();

        $this->assertFalse($policy->update($user, $audit));
    }

    public function test_delete_allows_when_user_has_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('delete_audit', true);
        $audit = $this->makeAuditMock();

        $this->assertTrue($policy->delete($user, $audit));
    }

    public function test_delete_denies_when_user_lacks_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('delete_audit', false);
        $audit = $this->makeAuditMock();

        $this->assertFalse($policy->delete($user, $audit));
    }

    public function test_deleteAny_allows_when_user_has_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('delete_any_audit', true);

        $this->assertTrue($policy->deleteAny($user));
    }

    public function test_deleteAny_denies_when_user_lacks_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('delete_any_audit', false);

        $this->assertFalse($policy->deleteAny($user));
    }

    public function test_forceDelete_allows_when_user_has_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('force_delete_audit', true);
        $audit = $this->makeAuditMock();

        $this->assertTrue($policy->forceDelete($user, $audit));
    }

    public function test_forceDelete_denies_when_user_lacks_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('force_delete_audit', false);
        $audit = $this->makeAuditMock();

        $this->assertFalse($policy->forceDelete($user, $audit));
    }

    public function test_forceDeleteAny_allows_when_user_has_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('force_delete_any_audit', true);

        $this->assertTrue($policy->forceDeleteAny($user));
    }

    public function test_forceDeleteAny_denies_when_user_lacks_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('force_delete_any_audit', false);

        $this->assertFalse($policy->forceDeleteAny($user));
    }

    public function test_restore_allows_when_user_has_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('restore_audit', true);
        $audit = $this->makeAuditMock();

        $this->assertTrue($policy->restore($user, $audit));
    }

    public function test_restore_denies_when_user_lacks_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('restore_audit', false);
        $audit = $this->makeAuditMock();

        $this->assertFalse($policy->restore($user, $audit));
    }

    public function test_restoreAny_allows_when_user_has_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('restore_any_audit', true);

        $this->assertTrue($policy->restoreAny($user));
    }

    public function test_restoreAny_denies_when_user_lacks_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('restore_any_audit', false);

        $this->assertFalse($policy->restoreAny($user));
    }

    public function test_replicate_allows_when_user_has_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('replicate_audit', true);
        $audit = $this->makeAuditMock();

        $this->assertTrue($policy->replicate($user, $audit));
    }

    public function test_replicate_denies_when_user_lacks_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('replicate_audit', false);
        $audit = $this->makeAuditMock();

        $this->assertFalse($policy->replicate($user, $audit));
    }

    public function test_reorder_allows_when_user_has_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('reorder_audit', true);

        $this->assertTrue($policy->reorder($user));
    }

    public function test_reorder_denies_when_user_lacks_permission()
    {
        $policy = new AuditPolicy();
        $user = $this->makeUserMockExpecting('reorder_audit', false);

        $this->assertFalse($policy->reorder($user));
    }
}