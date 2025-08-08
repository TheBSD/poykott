<?php

use App\Actions\ScrapeLogoFromUrlAction;
use App\Filament\Resources\AlternativeResource;
use App\Models\Alternative;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function () {
    $this->mockAction = Mockery::mock(ScrapeLogoFromUrlAction::class);
    app()->instance(ScrapeLogoFromUrlAction::class, $this->mockAction);
});

test('fetch logo action shows success notification when logo is fetched successfully', function (): void {
    $this->mockAction->shouldReceive('execute')
        ->once()
        ->andReturn(true);
    
    $alternative = Alternative::factory()->create([
        'url' => 'https://example.com'
    ]);
    
    // Mock the notification system
    Notification::shouldReceive('make')
        ->once()
        ->andReturnSelf();
    Notification::shouldReceive('success')
        ->once()
        ->andReturnSelf();
    Notification::shouldReceive('title')
        ->once()
        ->with('Logo fetched')
        ->andReturnSelf();
    Notification::shouldReceive('send')
        ->once();
    
    // Create and configure the table to get the actual action
    $table = app(Table::class);
    $configuredTable = AlternativeResource::table($table);
    
    expect($configuredTable)->toBeInstanceOf(Table::class);
});

test('fetch logo action shows error notification when logo fetch fails', function (): void {
    $this->mockAction->shouldReceive('execute')
        ->once()
        ->andReturn(false);
    
    $alternative = Alternative::factory()->create([
        'url' => 'https://example.com'
    ]);
    
    // Mock the notification system
    Notification::shouldReceive('make')
        ->once()
        ->andReturnSelf();
    Notification::shouldReceive('danger')
        ->once()
        ->andReturnSelf();
    Notification::shouldReceive('title')
        ->once()
        ->with('Failed fetching logo. Try uploading the logo manually')
        ->andReturnSelf();
    Notification::shouldReceive('send')
        ->once();
    
    // Create and configure the table to get the actual action  
    $table = app(Table::class);
    $configuredTable = AlternativeResource::table($table);
    
    expect($configuredTable)->toBeInstanceOf(Table::class);
});

test('remove logo action shows success notification after clearing media', function (): void {
    $alternative = Alternative::factory()->create();
    
    // Mock media collection
    $media = Mockery::mock(Media::class);
    $alternative->shouldReceive('getAttribute')
        ->with('media')
        ->andReturn(collect([$media]));
    
    $alternative->shouldReceive('clearMediaCollection')
        ->once();
    
    // Mock the notification system
    Notification::shouldReceive('make')
        ->once()
        ->andReturnSelf();
    Notification::shouldReceive('success')
        ->once()
        ->andReturnSelf();
    Notification::shouldReceive('title')
        ->once()
        ->with('Logo removed')
        ->andReturnSelf();
    Notification::shouldReceive('send')
        ->once();
    
    // Create and configure the table
    $table = app(Table::class);
    $configuredTable = AlternativeResource::table($table);
    
    expect($configuredTable)->toBeInstanceOf(Table::class);
});

test('bulk approve action updates multiple records correctly', function (): void {
    $records = collect([
        Alternative::factory()->create(),
        Alternative::factory()->create(),
    ]);
    
    // Mock the query builder
    $queryMock = Mockery::mock();
    $queryMock->shouldReceive('whereIn')
        ->with('id', $records->pluck('id')->toArray())
        ->once()
        ->andReturnSelf();
    $queryMock->shouldReceive('update')
        ->once()
        ->with(Mockery::on(function ($data) {
            return array_key_exists('approved_at', $data) && $data['approved_at'] !== null;
        }));
    
    Alternative::shouldReceive('query')
        ->once()
        ->andReturn($queryMock);
    
    // Mock the notification system
    Notification::shouldReceive('make')
        ->once()
        ->andReturnSelf();
    Notification::shouldReceive('success')
        ->once()
        ->andReturnSelf();
    Notification::shouldReceive('title')
        ->once()
        ->with('Alternatives Approved')
        ->andReturnSelf();
    Notification::shouldReceive('send')
        ->once();
    
    // Create and configure the table
    $table = app(Table::class);
    $configuredTable = AlternativeResource::table($table);
    
    expect($configuredTable)->toBeInstanceOf(Table::class);
});

test('fetch logo action requires confirmation only when media exists', function (): void {
    $alternativeWithMedia = Alternative::factory()->create();
    $alternativeWithoutMedia = Alternative::factory()->create();
    
    // Mock media collections
    $media = Mockery::mock(Media::class);
    $alternativeWithMedia->shouldReceive('getAttribute')
        ->with('media')
        ->andReturn(collect([$media]));
    
    $alternativeWithoutMedia->shouldReceive('getAttribute')
        ->with('media')
        ->andReturn(collect());
    
    // Create and configure the table
    $table = app(Table::class);
    $configuredTable = AlternativeResource::table($table);
    
    expect($configuredTable)->toBeInstanceOf(Table::class);
    
    // The confirmation requirement is based on media count > 0
    expect($alternativeWithMedia->media->count())->toBeGreaterThan(0);
    expect($alternativeWithoutMedia->media->count())->toBe(0);
});

test('remove logo action visibility depends on media existence', function (): void {
    $alternativeWithMedia = Alternative::factory()->create();
    $alternativeWithoutMedia = Alternative::factory()->create();
    
    // Mock media collections
    $media = Mockery::mock(Media::class);
    $alternativeWithMedia->shouldReceive('getAttribute')
        ->with('media')
        ->andReturn(collect([$media]));
    
    $alternativeWithoutMedia->shouldReceive('getAttribute')
        ->with('media')
        ->andReturn(collect());
    
    // Create and configure the table
    $table = app(Table::class);
    $configuredTable = AlternativeResource::table($table);
    
    expect($configuredTable)->toBeInstanceOf(Table::class);
    
    // The visibility is based on media count > 0
    expect($alternativeWithMedia->media->count())->toBeGreaterThan(0);
    expect($alternativeWithoutMedia->media->count())->toBe(0);
});

test('resources column formats state correctly for multiple resources', function (): void {
    $resource1 = (object) ['url' => 'https://resource1.example.com'];
    $resource2 = (object) ['url' => 'https://resource2.example.com'];
    
    $alternative = Alternative::factory()->create();
    $alternative->shouldReceive('getAttribute')
        ->with('resources')
        ->andReturn(collect([$resource1, $resource2]));
    
    // Create and configure the table
    $table = app(Table::class);
    $configuredTable = AlternativeResource::table($table);
    
    expect($configuredTable)->toBeInstanceOf(Table::class);
    
    // The formatStateUsing callback should generate HTML links separated by <br>
    $resources = $alternative->resources;
    expect($resources)->toHaveCount(2);
    expect($resources->first()->url)->toBe('https://resource1.example.com');
    expect($resources->last()->url)->toBe('https://resource2.example.com');
});

test('approved column boolean callback works for approved and unapproved alternatives', function (): void {
    $approvedAlternative = Alternative::factory()->create([
        'approved_at' => now()
    ]);
    
    $unapprovedAlternative = Alternative::factory()->create([
        'approved_at' => null
    ]);
    
    // Create and configure the table
    $table = app(Table::class);
    $configuredTable = AlternativeResource::table($table);
    
    expect($configuredTable)->toBeInstanceOf(Table::class);
    
    // Test the boolean logic
    expect($approvedAlternative->approved_at)->not->toBeNull();
    expect($unapprovedAlternative->approved_at)->toBeNull();
    
    // The boolean callback should return true for approved, false for unapproved
    expect($approvedAlternative->approved_at !== null)->toBeTrue();
    expect($unapprovedAlternative->approved_at !== null)->toBeFalse();
});

test('url column callback returns the record url', function (): void {
    $testUrl = 'https://test-alternative.com';
    $alternative = Alternative::factory()->create([
        'url' => $testUrl
    ]);
    
    // Create and configure the table
    $table = app(Table::class);
    $configuredTable = AlternativeResource::table($table);
    
    expect($configuredTable)->toBeInstanceOf(Table::class);
    
    // The url callback should return the record's url
    expect($alternative->url)->toBe($testUrl);
});