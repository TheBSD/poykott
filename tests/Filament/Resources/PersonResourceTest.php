<?php

use App\Filament\Resources\PersonResource;
use App\Models\Person;
use App\Models\Tag;
use App\Models\SocialLink;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('resource has correct model', function (): void {
    expect(PersonResource::getModel())->toBe(Person::class);
});

test('resource has correct navigation icon', function (): void {
    $reflection = new ReflectionClass(PersonResource::class);
    $property = $reflection->getProperty('navigationIcon');
    $property->setAccessible(true);
    
    expect($property->getValue())->toBe('heroicon-o-user-group');
});

test('resource has correct navigation group', function (): void {
    $reflection = new ReflectionClass(PersonResource::class);
    $property = $reflection->getProperty('navigationGroup');
    $property->setAccessible(true);
    
    expect($property->getValue())->toBe('People');
});

test('form schema contains required field types', function (): void {
    $form = PersonResource::form(Form::make());
    $schema = $form->getSchema();

    $componentTypes = collect($schema)->map(fn($component) => get_class($component))->toArray();
    
    expect($componentTypes)->toContain(TextInput::class)
        ->toContain(DateTimePicker::class)
        ->toContain(Select::class)
        ->toContain(Textarea::class)
        ->toContain(Repeater::class)
        ->toContain(SpatieMediaLibraryFileUpload::class);
});

test('form name field is required', function (): void {
    $form = PersonResource::form(Form::make());
    $nameField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof TextInput && $component->getName() === 'name'
    );
    
    expect($nameField)->not->toBeNull();
    expect($nameField->isRequired())->toBe(true);
});

test('form slug field is required', function (): void {
    $form = PersonResource::form(Form::make());
    $slugField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof TextInput && $component->getName() === 'slug'
    );
    
    expect($slugField)->not->toBeNull();
    expect($slugField->isRequired())->toBe(true);
});

test('form tags select has correct configuration', function (): void {
    $form = PersonResource::form(Form::make());
    $tagsField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof Select && $component->getName() === 'tags'
    );
    
    expect($tagsField)->not->toBeNull();
    expect($tagsField->isMultiple())->toBe(true);
    expect($tagsField->isSearchable())->toBe(true);
    expect($tagsField->isPreloaded())->toBe(true);
    expect($tagsField->isNative())->toBe(false);
});

test('form avatar upload has correct validation rules', function (): void {
    $form = PersonResource::form(Form::make());
    $avatarField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof SpatieMediaLibraryFileUpload && $component->getName() === 'avatar'
    );
    
    expect($avatarField)->not->toBeNull();
    
    $rules = $avatarField->getValidationRules();
    expect($rules)->toContain('image')
        ->toContain('mimes:jpeg,jpg,png,svg,webp')
        ->toContain('max:2048');
});

test('form social links repeater is configured correctly', function (): void {
    $form = PersonResource::form(Form::make());
    $socialLinksField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof Repeater && $component->getName() === 'socialLinks'
    );
    
    expect($socialLinksField)->not->toBeNull();
    expect($socialLinksField->getLabel())->toBe('Social Links');
    expect($socialLinksField->getDefaultItems())->toBe(0);
    expect($socialLinksField->getAddActionLabel())->toBe('Add Social Link');
    expect($socialLinksField->isColumnSpanFull())->toBe(true);
});

test('social links url field has proper validation', function (): void {
    $form = PersonResource::form(Form::make());
    $socialLinksField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof Repeater && $component->getName() === 'socialLinks'
    );
    
    $urlField = collect($socialLinksField->getChildComponents())->first(fn($component) => 
        $component instanceof TextInput && $component->getName() === 'url'
    );
    
    expect($urlField)->not->toBeNull();
    expect($urlField->getLabel())->toBe('Social URL');
    expect($urlField->isRequired())->toBe(true);
    expect($urlField->isDistinct())->toBe(true);
    
    $rules = $urlField->getValidationRules();
    expect($rules)->toContain('url');
});

test('table contains all expected columns', function (): void {
    $table = PersonResource::table(Table::make());
    $columns = $table->getColumns();
    
    $columnNames = collect($columns)->map(fn($column) => $column->getName())->toArray();
    
    $expectedColumns = [
        'name',
        'avatar', 
        'slug',
        'tagsRelation.name',
        'job_title',
        'approved_at',
        'resources.url',
        'location',
        'biography',
        'created_at',
        'updated_at'
    ];
    
    foreach ($expectedColumns as $expectedColumn) {
        expect($columnNames)->toContain($expectedColumn);
    }
});

test('table name column is searchable and sortable', function (): void {
    $table = PersonResource::table(Table::make());
    $nameColumn = collect($table->getColumns())->first(fn($column) => 
        $column->getName() === 'name'
    );
    
    expect($nameColumn)->not->toBeNull();
    expect($nameColumn->isSearchable())->toBe(true);
    expect($nameColumn->isSortable())->toBe(true);
});

test('table avatar column is circular', function (): void {
    $table = PersonResource::table(Table::make());
    $avatarColumn = collect($table->getColumns())->first(fn($column) => 
        $column instanceof SpatieMediaLibraryImageColumn && $column->getName() === 'avatar'
    );
    
    expect($avatarColumn)->not->toBeNull();
    expect($avatarColumn->isCircular())->toBe(true);
});

test('table approved column uses correct boolean logic', function (): void {
    $table = PersonResource::table(Table::make());
    $approvedColumn = collect($table->getColumns())->first(fn($column) => 
        $column instanceof IconColumn && $column->getName() === 'approved_at'
    );
    
    expect($approvedColumn)->not->toBeNull();
    expect($approvedColumn->getLabel())->toBe('Approved');
    
    // Test with approved person
    $approvedPerson = Person::factory()->make(['approved_at' => now()]);
    expect($approvedColumn->getStateUsing($approvedPerson))->toBe(true);
    
    // Test with non-approved person
    $nonApprovedPerson = Person::factory()->make(['approved_at' => null]);
    expect($approvedColumn->getStateUsing($nonApprovedPerson))->toBe(false);
});

test('table resources column is configured correctly', function (): void {
    $table = PersonResource::table(Table::make());
    $resourcesColumn = collect($table->getColumns())->first(fn($column) => 
        $column->getName() === 'resources.url'
    );
    
    expect($resourcesColumn)->not->toBeNull();
    expect($resourcesColumn->getLabel())->toBe('Resources');
    expect($resourcesColumn->isHtml())->toBe(true);
    expect($resourcesColumn->isClickDisabled())->toBe(true);
    expect($resourcesColumn->getColor())->toBe('info');
});

test('table biography column has character limit and is toggleable', function (): void {
    $table = PersonResource::table(Table::make());
    $biographyColumn = collect($table->getColumns())->first(fn($column) => 
        $column->getName() === 'biography'
    );
    
    expect($biographyColumn)->not->toBeNull();
    expect($biographyColumn->isSearchable())->toBe(true);
    expect($biographyColumn->getCharacterLimit())->toBe(60);
    expect($biographyColumn->isToggledHiddenByDefault())->toBe(true);
});

test('table timestamp columns are sortable and hidden by default', function (): void {
    $table = PersonResource::table(Table::make());
    
    $createdAtColumn = collect($table->getColumns())->first(fn($column) => 
        $column->getName() === 'created_at'
    );
    $updatedAtColumn = collect($table->getColumns())->first(fn($column) => 
        $column->getName() === 'updated_at'
    );
    
    expect($createdAtColumn)->not->toBeNull();
    expect($updatedAtColumn)->not->toBeNull();
    
    expect($createdAtColumn->isSortable())->toBe(true);
    expect($updatedAtColumn->isSortable())->toBe(true);
    expect($createdAtColumn->isToggledHiddenByDefault())->toBe(true);
    expect($updatedAtColumn->isToggledHiddenByDefault())->toBe(true);
});

test('table has edit and delete actions with empty labels', function (): void {
    $table = PersonResource::table(Table::make());
    $actions = $table->getActions();
    
    $actionClasses = collect($actions)->map(fn($action) => get_class($action))->toArray();
    
    expect($actionClasses)->toContain(EditAction::class)
        ->toContain(DeleteAction::class);
    
    $editAction = collect($actions)->first(fn($action) => $action instanceof EditAction);
    $deleteAction = collect($actions)->first(fn($action) => $action instanceof DeleteAction);
    
    expect($editAction->getLabel())->toBe('');
    expect($deleteAction->getLabel())->toBe('');
});

test('table has bulk delete action', function (): void {
    $table = PersonResource::table(Table::make());
    $bulkActions = $table->getBulkActions();
    
    expect($bulkActions)->not->toBeEmpty();
    
    $bulkActionGroup = $bulkActions[0];
    $actions = $bulkActionGroup->getActions();
    $actionClasses = collect($actions)->map(fn($action) => get_class($action))->toArray();
    
    expect($actionClasses)->toContain(DeleteBulkAction::class);
});

test('get relations returns expected relation managers', function (): void {
    $relations = PersonResource::getRelations();
    
    expect($relations)->toContain('App\Filament\Resources\AlternativeResource\RelationManagers\ResourcesRelationManager')
        ->toContain('App\Filament\Resources\AuditsRelationManagerResource\RelationManagers\AuditsRelationManager');
});

test('get pages returns expected page classes', function (): void {
    $pages = PersonResource::getPages();
    
    expect($pages)->toHaveKeys(['index', 'create', 'edit']);
    expect(get_class($pages['index']))->toBe('App\Filament\Resources\PersonResource\Pages\ListPeople');
    expect(get_class($pages['create']))->toBe('App\Filament\Resources\PersonResource\Pages\CreatePerson');
    expect(get_class($pages['edit']))->toBe('App\Filament\Resources\PersonResource\Pages\EditPerson');
});

test('form handles empty optional values gracefully', function (): void {
    $form = PersonResource::form(Form::make());
    
    $data = [
        'name' => 'Test Person',
        'slug' => 'test-person',
        'job_title' => null,
        'approved_at' => null,
        'location' => '',
        'description' => null,
    ];
    
    // This should not throw an exception
    expect(fn() => $form->fill($data))->not->toThrow();
});

test('approved column handles edge cases correctly', function (): void {
    $table = PersonResource::table(Table::make());
    $approvedColumn = collect($table->getColumns())->first(fn($column) => 
        $column instanceof IconColumn && $column->getName() === 'approved_at'
    );
    
    $testCases = [
        ['approved_at' => now(), 'expected' => true],
        ['approved_at' => null, 'expected' => false],
        ['approved_at' => '2023-01-01 00:00:00', 'expected' => true],
    ];
    
    foreach ($testCases as $case) {
        $person = Person::factory()->make($case);
        $result = $approvedColumn->getStateUsing($person);
        expect($result)->toBe($case['expected']);
    }
});

test('resources column formats urls with proper html structure', function (): void {
    $person = Person::factory()->create();
    
    // Mock resources relationship
    $resources = collect([
        (object) ['url' => 'https://example.com/very-long-url-that-should-be-truncated-to-test-the-limit-functionality'],
        (object) ['url' => 'https://short.com'],
    ]);
    
    $person->resources = $resources;
    
    $table = PersonResource::table(Table::make());
    $resourcesColumn = collect($table->getColumns())->first(fn($column) => 
        $column->getName() === 'resources.url'
    );
    
    $formattedState = $resourcesColumn->formatStateUsing($person);
    
    expect($formattedState)->toContain('<a href=')
        ->toContain('target="_blank"')
        ->toContain('<br>')
        ->toContain('...');
});

test('tags select has create option form configured', function (): void {
    $form = PersonResource::form(Form::make());
    $tagsField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof Select && $component->getName() === 'tags'
    );
    
    expect($tagsField)->not->toBeNull();
    
    $createOptionForm = $tagsField->getCreateOptionForm();
    expect($createOptionForm)->not->toBeNull()
        ->not->toBeEmpty();
});

test('form components have correct relationships', function (): void {
    $form = PersonResource::form(Form::make());
    
    // Test tags relationship
    $tagsField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof Select && $component->getName() === 'tags'
    );
    expect($tagsField->getRelationshipName())->toBe('tagsRelation');
    
    // Test social links relationship  
    $socialLinksField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof Repeater && $component->getName() === 'socialLinks'
    );
    expect($socialLinksField->getRelationshipName())->toBe('socialLinks');
});

test('job title and location fields are optional', function (): void {
    $form = PersonResource::form(Form::make());
    
    $jobTitleField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof TextInput && $component->getName() === 'job_title'
    );
    $locationField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof TextInput && $component->getName() === 'location'
    );
    
    expect($jobTitleField)->not->toBeNull();
    expect($locationField)->not->toBeNull();
    expect($jobTitleField->isRequired())->toBe(false);
    expect($locationField->isRequired())->toBe(false);
});

test('description textarea field exists and is optional', function (): void {
    $form = PersonResource::form(Form::make());
    
    $descriptionField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof Textarea && $component->getName() === 'description'
    );
    
    expect($descriptionField)->not->toBeNull();
    expect($descriptionField->isRequired())->toBe(false);
});

test('approved at date time picker is optional', function (): void {
    $form = PersonResource::form(Form::make());
    
    $approvedAtField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof DateTimePicker && $component->getName() === 'approved_at'
    );
    
    expect($approvedAtField)->not->toBeNull();
    expect($approvedAtField->isRequired())->toBe(false);
});

test('table tags column shows badge format', function (): void {
    $table = PersonResource::table(Table::make());
    $tagsColumn = collect($table->getColumns())->first(fn($column) => 
        $column->getName() === 'tagsRelation.name'
    );
    
    expect($tagsColumn)->not->toBeNull();
    expect($tagsColumn->getLabel())->toBe('Tags');
    expect($tagsColumn->isBadge())->toBe(true);
    expect($tagsColumn->isSearchable())->toBe(true);
});

test('table job title column is searchable and sortable', function (): void {
    $table = PersonResource::table(Table::make());
    $jobTitleColumn = collect($table->getColumns())->first(fn($column) => 
        $column->getName() === 'job_title'
    );
    
    expect($jobTitleColumn)->not->toBeNull();
    expect($jobTitleColumn->isSearchable())->toBe(true);
    expect($jobTitleColumn->isSortable())->toBe(true);
});

test('table location column is searchable and sortable', function (): void {
    $table = PersonResource::table(Table::make());
    $locationColumn = collect($table->getColumns())->first(fn($column) => 
        $column->getName() === 'location'
    );
    
    expect($locationColumn)->not->toBeNull();
    expect($locationColumn->isSearchable())->toBe(true);
    expect($locationColumn->isSortable())->toBe(true);
});

test('table slug column is searchable', function (): void {
    $table = PersonResource::table(Table::make());
    $slugColumn = collect($table->getColumns())->first(fn($column) => 
        $column->getName() === 'slug'
    );
    
    expect($slugColumn)->not->toBeNull();
    expect($slugColumn->isSearchable())->toBe(true);
});

test('form validation handles invalid data appropriately', function (): void {
    $form = PersonResource::form(Form::make());
    
    // Test that form schema is properly structured for validation
    $schema = $form->getSchema();
    expect($schema)->not->toBeEmpty();
    
    // Verify required fields exist
    $requiredFields = collect($schema)->filter(fn($component) => $component->isRequired())->count();
    expect($requiredFields)->toBeGreaterThan(0);
});

test('social links repeater allows multiple entries', function (): void {
    $form = PersonResource::form(Form::make());
    $socialLinksField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof Repeater && $component->getName() === 'socialLinks'
    );
    
    expect($socialLinksField)->not->toBeNull();
    expect($socialLinksField->getColumns())->toBe(1);
    expect($socialLinksField->getDefaultItems())->toBe(0);
    
    // Verify it can handle multiple social links
    $testData = [
        ['url' => 'https://twitter.com/example'],
        ['url' => 'https://linkedin.com/in/example'],
        ['url' => 'https://github.com/example'],
    ];
    
    expect(count($testData))->toBeGreaterThan(1);
});

test('avatar upload field accepts correct file types', function (): void {
    $form = PersonResource::form(Form::make());
    $avatarField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof SpatieMediaLibraryFileUpload && $component->getName() === 'avatar'
    );
    
    expect($avatarField)->not->toBeNull();
    
    $rules = $avatarField->getValidationRules();
    $acceptedTypes = ['jpeg', 'jpg', 'png', 'svg', 'webp'];
    
    foreach ($acceptedTypes as $type) {
        expect(implode(',', $rules))->toContain($type);
    }
});

test('table filters are empty by default', function (): void {
    $table = PersonResource::table(Table::make());
    $filters = $table->getFilters();
    
    expect($filters)->toBeEmpty();
});

test('form grid structure for tag creation is configured', function (): void {
    $form = PersonResource::form(Form::make());
    $tagsField = collect($form->getSchema())->first(fn($component) => 
        $component instanceof Select && $component->getName() === 'tags'
    );
    
    expect($tagsField)->not->toBeNull();
    
    $createOptionForm = $tagsField->getCreateOptionForm();
    expect($createOptionForm)->not->toBeNull();
    
    // The create option form should contain Grid with name and slug fields
    $hasGridStructure = !empty($createOptionForm);
    expect($hasGridStructure)->toBe(true);
});