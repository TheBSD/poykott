<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Company;
use App\Models\User;
use App\Policies\CompanyPolicy;
use PHPUnit\Framework\TestCase;


final class CompanyPolicyTest extends TestCase
{
    /**
     * Data provider mapping policy methods to their required permission and whether a Company model is required.
     *
     * @return array<string, array{0:string, 1:bool}>
     */
    public function policyMethodProvider(): array
    {
        return [
            'viewAny requires view_any_company' => ['viewAny', true, false],
            'view requires view_company' => ['view', true, true],
            'create requires create_company' => ['create', true, false],
            'update requires update_company' => ['update', true, true],
            'delete requires delete_company' => ['delete', true, true],
            'deleteAny requires delete_any_company' => ['deleteAny', true, false],
            'forceDelete requires force_delete_company' => ['forceDelete', true, true],
            'forceDeleteAny requires force_delete_any_company' => ['forceDeleteAny', true, false],
            'restore requires restore_company' => ['restore', true, true],
            'restoreAny requires restore_any_company' => ['restoreAny', true, false],
            'replicate requires replicate_company' => ['replicate', true, true],
            'reorder requires reorder_company' => ['reorder', true, false],
        ];
    }

    /**
     * Data provider mapping policy methods to their expected permission string and whether a Company parameter is required.
     *
     * @return array<string, array{0:string, 1:string, 2:bool}>
     */
    public function policyAbilitiesProvider(): array
    {
        return [
            'viewAny'        => ['viewAny', 'view_any_company', false],
            'view'           => ['view', 'view_company', true],
            'create'         => ['create', 'create_company', false],
            'update'         => ['update', 'update_company', true],
            'delete'         => ['delete', 'delete_company', true],
            'deleteAny'      => ['deleteAny', 'delete_any_company', false],
            'forceDelete'    => ['forceDelete', 'force_delete_company', true],
            'forceDeleteAny' => ['forceDeleteAny', 'force_delete_any_company', false],
            'restore'        => ['restore', 'restore_company', true],
            'restoreAny'     => ['restoreAny', 'restore_any_company', false],
            'replicate'      => ['replicate', 'replicate_company', true],
            'reorder'        => ['reorder', 'reorder_company', false],
        ];
    }

    /**
     * Ensures each policy method returns true when the user has the required permission
     * and invokes User::can exactly once with the expected permission string.
     *
     * @dataProvider policyAbilitiesProvider
     */
    public function test_policy_methods_return_true_with_required_permission(string $method, string $permission, bool $needsCompany): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('can')
            ->with($permission)
            ->willReturn(true);

        $policy = new CompanyPolicy();

        $result = $needsCompany
            ? $policy->{$method}($user, $this->createMock(Company::class))
            : $policy->{$method}($user);

        $this->assertTrue($result, "Expected {$method} to return true when user can '{$permission}'.");
    }

    /**
     * Ensures each policy method returns false when the user lacks the required permission
     * and invokes User::can exactly once with the expected permission string.
     *
     * @dataProvider policyAbilitiesProvider
     */
    public function test_policy_methods_return_false_without_required_permission(string $method, string $permission, bool $needsCompany): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('can')
            ->with($permission)
            ->willReturn(false);

        $policy = new CompanyPolicy();

        $result = $needsCompany
            ? $policy->{$method}($user, $this->createMock(Company::class))
            : $policy->{$method}($user);

        $this->assertFalse($result, "Expected {$method} to return false when user cannot '{$permission}'.");
    }

    /**
     * Verify that each policy method uses the precise expected permission name,
     * not any nearby or similar permission. We simulate that any other permission would
     * return true, but the exact expected one returns false; the policy should still return false.
     *
     * @dataProvider policyAbilitiesProvider
     */
    public function test_policy_methods_use_exact_permission_name(string $method, string $expectedPermission, bool $needsCompany): void
    {
        $user = $this->getMockBuilder(User::class)
            ->onlyMethods(['can'])
            ->getMock();

        // If policy calls any permission other than the expected one, return true to catch mis-wiring.
        $user->expects($this->once())
            ->method('can')
            ->with($expectedPermission)
            ->willReturn(false);

        $policy = new CompanyPolicy();

        $result = $needsCompany
            ? $policy->{$method}($user, $this->createMock(Company::class))
            : $policy->{$method}($user);

        $this->assertFalse(
            $result,
            "Policy method '{$method}' should call can('{$expectedPermission}') exactly. If it used a different permission, this test would incorrectly pass."
        );
    }
}
