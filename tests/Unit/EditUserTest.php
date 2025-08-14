<?php

declare(strict_types=1);

/*
Testing framework: Pest PHP v3 with pest-plugin-laravel.
These are unit tests focused on the diff for EditUser:
- getRedirectUrl returns the resource index URL
- handleRecordUpdate strips "roles", updates attributes, and conditionally syncs a single role
Approach: Use lightweight doubles to avoid DB/Livewire/Filament runtime.
*/

use Illuminate\Database\Eloquent\Model;

class FakeRelation
{
    /** @var array<int, int|string> */
    public array $synced = [];
    public int $syncCalls = 0;

    /** @param array<int, int|string> $ids */
    public function sync(array $ids): void
    {
        $this->syncCalls++;
        $this->synced = $ids;
    }
}

class FakeUserModel extends Model
{
    /** @var array<string, mixed> */
    public array $updatedData = [];

    public FakeRelation $relation;

    public function __construct()
    {
        // Avoid Eloquent/DB setup entirely.
        $this->relation = new FakeRelation();
    }

    /**
     * Override to avoid DB; just record the data passed in.
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $_options
     */
    public function update(array $attributes = [], array $_options = []): bool
    {
        (void) $_options;
        $this->updatedData = $attributes;
        return true;
    }

    public function roles(): FakeRelation
    {
        return $this->relation;
    }
}

class StubUserResource
{
    public static function getUrl(string $page, array $_parameters = []): string
    {
        (void) $_parameters;
        return '/stubbed-resource/' . $page;
    }
}

/**
 * Testable subclass that:
 * - Overrides static $resource to our stub
 * - Exposes wrappers to protected methods
 * - Skips Livewire/Filament init by not calling parent constructor
 */
class TestableEditUser extends \App\Filament\Resources\UserResource\Pages\EditUser
{
    protected static string $resource = \StubUserResource::class;

    public function __construct()
    {
        // Intentionally empty; no parent init.
    }

    /** @param array<string, mixed> $data */
    public function callHandleRecordUpdate(Model $record, array $data): Model
    {
        return parent::handleRecordUpdate($record, $data);
    }

    public function callGetRedirectUrl(): string
    {
        return $this->getRedirectUrl();
    }
}

test('handleRecordUpdate updates without roles and does not sync', function (): void {
    $page = new TestableEditUser();
    $record = new FakeUserModel();

    $input = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.test',
        // no 'roles' key present
    ];

    $returned = $page->callHandleRecordUpdate($record, $input);

    expect($returned)->toBe($record);
    expect($record->updatedData)->toEqual([
        'name' => 'Jane Doe',
        'email' => 'jane@example.test',
    ]);
    expect($record->relation->syncCalls)->toBe(0);
    expect($record->relation->synced)->toEqual([]);
});

test('handleRecordUpdate syncs role when numeric role id is present', function (): void {
    $page = new TestableEditUser();
    $record = new FakeUserModel();

    $input = [
        'name' => 'John Doe',
        'email' => 'john@example.test',
        'roles' => 42,
    ];

    $returned = $page->callHandleRecordUpdate($record, $input);

    expect($returned)->toBe($record);
    expect($record->updatedData)->toEqual([
        'name' => 'John Doe',
        'email' => 'john@example.test',
    ]);
    expect($record->relation->syncCalls)->toBe(1);
    expect($record->relation->synced)->toEqual([42]);
});

test('handleRecordUpdate syncs role when non-numeric string value is present', function (): void {
    $page = new TestableEditUser();
    $record = new FakeUserModel();

    $input = [
        'name' => 'Ann',
        'roles' => 'admin',
    ];

    $page->callHandleRecordUpdate($record, $input);

    expect($record->updatedData)->toEqual(['name' => 'Ann']);
    expect($record->relation->syncCalls)->toBe(1);
    expect($record->relation->synced)->toEqual(['admin']);
});

test('handleRecordUpdate does not sync for empty-like role values', function (): void {
    $page = new TestableEditUser();

    $cases = [
        null,
        '',
        0,
        [],
        '0', // string zero is falsy in PHP
    ];

    foreach ($cases as $idx => $roleValue) {
        $record = new FakeUserModel();
        $input = [
            'name' => 'Case ' . $idx,
            'roles' => $roleValue,
        ];

        $returned = $page->callHandleRecordUpdate($record, $input);

        expect($returned)->toBe($record);
        expect($record->updatedData)->toEqual(['name' => 'Case ' . $idx]);
        expect($record->relation->syncCalls)->toBe(0, "Case $idx: sync should not be called");
        expect($record->relation->synced)->toEqual([]);
    }
});

test('getRedirectUrl uses resource index URL', function (): void {
    $page = new TestableEditUser();

    $url = $page->callGetRedirectUrl();

    expect($url)->toBe('/stubbed-resource/index');
});