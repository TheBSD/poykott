<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Filament\Resources\UserResource\Pages\CreateUser;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

/**
 * Note: This test suite is written for PHPUnit, which is the predominant testing framework in Laravel ecosystems.
 * If the project uses Pest, these PHPUnit tests will still run under Pest as-is.
 *
 * We avoid touching the database and Filament internals by stubbing:
 * - A TestableCreateUser page class overriding resource/model resolution.
 * - A FakeUser Eloquent-like model with an overridden static create() to avoid persistence.
 * - A Fake relation object implementing sync().
 * - A FakeUserResource with getUrl() for redirection testing.
 */
final class CreateUserTest extends TestCase
{
    /**
     * Build a fresh page instance for each test using stubs.
     */
    private function makePage(): TestableCreateUser
    {
        return new TestableCreateUser();
    }

    public function test_handle_record_creation_with_role_syncs_and_strips_roles_from_payload(): void
    {
        $page = $this->makePage();

        $input = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'secret',
            'roles' => 5,
            'ignored' => null,
        ];

        /** @var FakeUser $user */
        $user = $this->invokeHandleRecordCreation($page, $input);

        // Asserts
        $this->assertInstanceOf(Model::class, $user, 'Should return an Eloquent Model instance');
        $this->assertSame('Jane Doe', $user->attributes['name'] ?? null);
        $this->assertSame('jane@example.com', $user->attributes['email'] ?? null);
        $this->assertSame('secret', $user->attributes['password'] ?? null);

        // roles must be removed from the attributes before creation
        $this->assertArrayNotHasKey('roles', $user->attributes, 'roles key should be stripped before create');

        // sync is called with the single role ID wrapped in an array
        $this->assertSame([[5]], $user->syncedRolesCalls, 'roles()->sync should be called once with [5]');
    }

    public function test_handle_record_creation_without_roles_does_not_sync(): void
    {
        $page = $this->makePage();

        $input = [
            'name' => 'John NoRole',
            'email' => 'john@example.com',
            'password' => 'topsecret',
            // no roles key
        ];

        /** @var FakeUser $user */
        $user = $this->invokeHandleRecordCreation($page, $input);

        $this->assertSame('John NoRole', $user->attributes['name'] ?? null);
        $this->assertArrayNotHasKey('roles', $user->attributes);
        $this->assertSame([], $user->syncedRolesCalls, 'No roles sync should occur when roles are absent');
    }

    public function test_handle_record_creation_with_falsy_role_zero_or_empty_string_does_not_sync(): void
    {
        $page = $this->makePage();

        // roles = 0
        $userZero = $this->invokeHandleRecordCreation($page, [
            'name' => 'Zero',
            'roles' => 0,
        ]);
        $this->assertSame([], $userZero->syncedRolesCalls, 'roles=0 is falsy; sync should not be called');

        // roles = ''
        $userEmpty = $this->invokeHandleRecordCreation($page, [
            'name' => 'Empty',
            'roles' => '',
        ]);
        $this->assertSame([], $userEmpty->syncedRolesCalls, "roles='' is falsy; sync should not be called");
    }

    public function test_handle_record_creation_with_array_roles_results_in_nested_array_sync(): void
    {
        $page = $this->makePage();

        $input = [
            'name' => 'Array Role',
            'roles' => [1, 2],
        ];

        /** @var FakeUser $user */
        $user = $this->invokeHandleRecordCreation($page, $input);

        // Current implementation wraps whatever comes in as $role into another array: sync([$role])
        // If roles is already an array, this becomes [[1, 2]], which we document via this test.
        $this->assertSame([[ [1, 2] ]], $user->syncedRolesCalls, 'Nested array indicates potential input contract mismatch');
    }

    public function test_get_redirect_url_delegates_to_resource_index_url(): void
    {
        $page = $this->makePage();

        $url = $this->invokeGetRedirectUrl($page);

        $this->assertSame('/fake/users/index', $url);
        $this->assertSame(
            ['index'],
            FakeUserResource::$getUrlCalls,
            'getUrl should be called with the "index" route name'
        );
    }

    /**
     * Helper to call protected handleRecordCreation.
     * @param TestableCreateUser $page
     * @param array $data
     * @return FakeUser
     */
    private function invokeHandleRecordCreation(TestableCreateUser $page, array $data): FakeUser
    {
        $ref = new \ReflectionClass($page);
        $method = $ref->getMethod('handleRecordCreation');
        $method->setAccessible(true);

        /** @var FakeUser $result */
        $result = $method->invoke($page, $data);
        $this->assertInstanceOf(FakeUser::class, $result);

        return $result;
    }

    /**
     * Helper to call protected getRedirectUrl.
     */
    private function invokeGetRedirectUrl(TestableCreateUser $page): string
    {
        $ref = new \ReflectionClass($page);
        $method = $ref->getMethod('getRedirectUrl');
        $method->setAccessible(true);

        return (string) $method->invoke($page);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // reset static trackers
        FakeUserResource::$getUrlCalls = [];
        FakeUser::$createdPayloads = [];
    }
}

/**
 * Test stub: override model/resource resolution to avoid depending on Filament internals.
 */
class TestableCreateUser extends CreateUser
{
    // Override the resource class to our fake
    protected static string $resource = FakeUserResource::class;

    // Some Filament parents call static::getModel(); provide it.
    public static function getModel(): string
    {
        return FakeUser::class;
    }

    // Ensure instance-level resource resolution returns the fake, used by getRedirectUrl()
    protected function getResource(): string
    {
        return FakeUserResource::class;
    }
}

/**
 * Minimal fake Filament Resource for URL generation assertions.
 */
class FakeUserResource
{
    public static array $getUrlCalls = [];

    public static function getUrl(string $name): string
    {
        self::$getUrlCalls[] = $name;
        // Return a predictable URL for assertions
        if ($name === 'index') {
            return '/fake/users/index';
        }
        return '/fake/unknown';
    }
}

/**
 * Minimal fake Eloquent-like model that avoids DB access.
 * - Overrides static create() to simply instantiate and capture attributes.
 * - Provides a roles() relation stub whose sync() method records calls.
 */
class FakeUser extends Model
{
    /** @var array<string, mixed> */
    public array $attributes = [];

    /** @var array<int, array<int, mixed>> Record each sync call's payload */
    public array $syncedRolesCalls = [];

    /** @var array<int, array<string, mixed>> */
    public static array $createdPayloads = [];

    /**
     * Override Eloquent's dynamic static create to avoid DB.
     * @param array<string, mixed> $attributes
     * @return static
     */
    public static function create(array $attributes = [])
    {
        $instance = new static();
        // Simulate mass-assignable attributes capture
        $instance->attributes = $attributes;
        self::$createdPayloads[] = $attributes;

        return $instance;
    }

    /**
     * Return a fake relation object with sync() support.
     */
    public function roles(): FakeBelongsToMany
    {
        return new FakeBelongsToMany($this);
    }
}

/**
 * Minimal relation stub representing belongsToMany with sync().
 */
class FakeBelongsToMany
{
    public function __construct(private FakeUser $parent)
    {
    }

    /**
     * Record sync calls on the parent fake model.
     * @param array<int, mixed> $ids
     * @return void
     */
    public function sync(array $ids): void
    {
        // Capture the raw array passed to sync, for precise assertions
        $this->parent->syncedRolesCalls[] = $ids;
    }
}