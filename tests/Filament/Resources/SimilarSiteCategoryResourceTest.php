<?php

use App\Filament\Resources\SimilarSiteCategoryResource;
use App\Filament\Resources\SimilarSiteCategoryResource\Pages\CreateSimilarSiteCategory;
use App\Filament\Resources\SimilarSiteCategoryResource\Pages\EditSimilarSiteCategory;
use App\Filament\Resources\SimilarSiteCategoryResource\Pages\ListSimilarSiteCategories;
use App\Filament\Resources\AuditsRelationManagerResource\RelationManagers\AuditsRelationManager;
use App\Models\SimilarSiteCategory;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

beforeEach(function () {
    $this->resource = new SimilarSiteCategoryResource();
    $this->model = SimilarSiteCategory::factory()->create([
        'name' => 'Test Category',
        'description' => 'Test Description'
    ]);
});

// Core Resource Configuration Tests
test('resource has correct model binding', function (): void {
    expect(SimilarSiteCategoryResource::getModel())
        ->toBe(SimilarSiteCategory::class);
});

test('resource has correct navigation icon', function (): void {
    $reflection = new ReflectionClass(SimilarSiteCategoryResource::class);
    $property = $reflection->getProperty('navigationIcon');
    $property->setAccessible(true);
    
    expect($property->getValue())
        ->toBe('heroicon-o-rectangle-group');
});

test('resource has correct navigation group', function (): void {
    $reflection = new ReflectionClass(SimilarSiteCategoryResource::class);
    $property = $reflection->getProperty('navigationGroup'); 
    $property->setAccessible(true);
    
    expect($property->getValue())
        ->toBe('Similar Sites');
});

// Form Configuration Tests
test('form creates required name field correctly', function (): void {
    $form = new Form($this->resource);
    $formSchema = SimilarSiteCategoryResource::form($form);
    
    expect($formSchema)->toBeInstanceOf(Form::class);
    
    $schema = $formSchema->getSchema();
    expect($schema)->toHaveCount(2);
    
    // Test name field
    $nameField = $schema[0];
    expect($nameField)->toBeInstanceOf(TextInput::class);
    expect($nameField->getName())->toBe('name');
    expect($nameField->isRequired())->toBeTrue();
});

test('form creates description textarea field correctly', function (): void {
    $form = new Form($this->resource);
    $formSchema = SimilarSiteCategoryResource::form($form);
    
    $schema = $formSchema->getSchema();
    
    // Test description field
    $descriptionField = $schema[1];
    expect($descriptionField)->toBeInstanceOf(Textarea::class);
    expect($descriptionField->getName())->toBe('description');
    expect($descriptionField->canSpanFullWidth())->toBeTrue();
    expect($descriptionField->isRequired())->toBeFalse();
});

test('form field validation rules are correctly configured', function (): void {
    $form = new Form($this->resource);
    $formSchema = SimilarSiteCategoryResource::form($form);
    $schema = $formSchema->getSchema();
    
    // Name field should be required
    $nameField = $schema[0];
    expect($nameField->isRequired())->toBeTrue();
    
    // Description field should be optional
    $descriptionField = $schema[1];
    expect($descriptionField->isRequired())->toBeFalse();
});

test('form fields correspond to model fillable attributes', function (): void {
    $form = new Form($this->resource);
    $formSchema = SimilarSiteCategoryResource::form($form);
    $schema = $formSchema->getSchema();
    
    $fieldNames = array_map(fn($field) => $field->getName(), $schema);
    
    expect($fieldNames)->toContain('name');
    expect($fieldNames)->toContain('description');
});

test('form field types match expected input types', function (): void {
    $form = new Form($this->resource);
    $formSchema = SimilarSiteCategoryResource::form($form);
    $schema = $formSchema->getSchema();
    
    // Name should be TextInput
    expect($schema[0])->toBeInstanceOf(TextInput::class);
    
    // Description should be Textarea
    expect($schema[1])->toBeInstanceOf(Textarea::class);
});

// Table Configuration Tests
test('table has correct columns configuration', function (): void {
    $table = new Table($this->resource);
    $tableSchema = SimilarSiteCategoryResource::table($table);
    
    expect($tableSchema)->toBeInstanceOf(Table::class);
    
    $columns = $tableSchema->getColumns();
    expect($columns)->toHaveCount(3);
    
    // Test name column
    $nameColumn = $columns[0];
    expect($nameColumn)->toBeInstanceOf(TextColumn::class);
    expect($nameColumn->getName())->toBe('name');
    expect($nameColumn->isSearchable())->toBeTrue();
    
    // Test created_at column
    $createdAtColumn = $columns[1];
    expect($createdAtColumn)->toBeInstanceOf(TextColumn::class);
    expect($createdAtColumn->getName())->toBe('created_at');
    expect($createdAtColumn->isSortable())->toBeTrue();
    expect($createdAtColumn->isToggledHiddenByDefault())->toBeTrue();
    
    // Test updated_at column
    $updatedAtColumn = $columns[2];
    expect($updatedAtColumn)->toBeInstanceOf(TextColumn::class);
    expect($updatedAtColumn->getName())->toBe('updated_at');
    expect($updatedAtColumn->isSortable())->toBeTrue();
    expect($updatedAtColumn->isToggledHiddenByDefault())->toBeTrue();
});

test('table columns have correct display and interaction properties', function (): void {
    $table = new Table($this->resource);
    $tableSchema = SimilarSiteCategoryResource::table($table);
    $columns = $tableSchema->getColumns();
    
    $nameColumn = $columns[0];
    $createdAtColumn = $columns[1];
    $updatedAtColumn = $columns[2];
    
    // Name column should be searchable
    expect($nameColumn->isSearchable())->toBeTrue();
    
    // Date columns should be sortable and toggleable hidden by default
    expect($createdAtColumn->isSortable())->toBeTrue();
    expect($updatedAtColumn->isSortable())->toBeTrue();
    expect($createdAtColumn->isToggledHiddenByDefault())->toBeTrue();
    expect($updatedAtColumn->isToggledHiddenByDefault())->toBeTrue();
});

test('table columns match expected database schema', function (): void {
    $table = new Table($this->resource);
    $tableSchema = SimilarSiteCategoryResource::table($table);
    $columns = $tableSchema->getColumns();
    
    $columnNames = array_map(fn($column) => $column->getName(), $columns);
    
    expect($columnNames)->toContain('name');
    expect($columnNames)->toContain('created_at');
    expect($columnNames)->toContain('updated_at');
});

test('datetime columns are properly formatted', function (): void {
    $table = new Table($this->resource);
    $tableSchema = SimilarSiteCategoryResource::table($table);
    $columns = $tableSchema->getColumns();
    
    $createdAtColumn = $columns[1];
    $updatedAtColumn = $columns[2];
    
    // Check that datetime formatting is applied
    expect($createdAtColumn->getFormatState())->not->toBeNull();
    expect($updatedAtColumn->getFormatState())->not->toBeNull();
});

// Table Actions Tests
test('table has edit action configured', function (): void {
    $table = new Table($this->resource);
    $tableSchema = SimilarSiteCategoryResource::table($table);
    
    $actions = $tableSchema->getActions();
    expect($actions)->toHaveCount(1);
    
    $editAction = $actions[0];
    expect($editAction)->toBeInstanceOf(EditAction::class);
});

test('table has bulk delete action configured', function (): void {
    $table = new Table($this->resource);
    $tableSchema = SimilarSiteCategoryResource::table($table);
    
    $bulkActions = $tableSchema->getBulkActions();
    expect($bulkActions)->toHaveCount(1);
    
    $bulkActionGroup = $bulkActions[0];
    expect($bulkActionGroup)->toBeInstanceOf(BulkActionGroup::class);
    
    $actions = $bulkActionGroup->getActions();
    expect($actions)->toHaveCount(1);
    
    $deleteAction = $actions[0];
    expect($deleteAction)->toBeInstanceOf(DeleteBulkAction::class);
});

test('table actions are properly configured', function (): void {
    $table = new Table($this->resource);
    $tableSchema = SimilarSiteCategoryResource::table($table);
    
    // Should have edit action
    $actions = $tableSchema->getActions();
    expect($actions)->not->toBeEmpty();
    expect($actions[0])->toBeInstanceOf(EditAction::class);
    
    // Should have bulk delete action
    $bulkActions = $tableSchema->getBulkActions();
    expect($bulkActions)->not->toBeEmpty();
    expect($bulkActions[0])->toBeInstanceOf(BulkActionGroup::class);
});

test('table has no filters configured', function (): void {
    $table = new Table($this->resource);
    $tableSchema = SimilarSiteCategoryResource::table($table);
    
    $filters = $tableSchema->getFilters();
    expect($filters)->toBeEmpty();
});

// Relations and Pages Tests
test('resource returns correct relations', function (): void {
    $relations = SimilarSiteCategoryResource::getRelations();
    
    expect($relations)
        ->toBeArray()
        ->toHaveCount(1)
        ->toContain(AuditsRelationManager::class);
});

test('relation manager is properly configured', function (): void {
    $relations = SimilarSiteCategoryResource::getRelations();
    
    expect($relations)->toContain(AuditsRelationManager::class);
    expect(class_exists(AuditsRelationManager::class))->toBeTrue();
});

test('resource returns correct pages configuration', function (): void {
    $pages = SimilarSiteCategoryResource::getPages();
    
    expect($pages)
        ->toBeArray()
        ->toHaveCount(3)
        ->toHaveKeys(['index', 'create', 'edit']);
    
    expect(get_class($pages['index']->getPage()))->toBe(ListSimilarSiteCategories::class);
    expect(get_class($pages['create']->getPage()))->toBe(CreateSimilarSiteCategory::class);
    expect(get_class($pages['edit']->getPage()))->toBe(EditSimilarSiteCategory::class);
});

test('pages have correct route patterns', function (): void {
    $pages = SimilarSiteCategoryResource::getPages();
    
    expect($pages['index']->getRoute())->toBe('/');
    expect($pages['create']->getRoute())->toBe('/create');
    expect($pages['edit']->getRoute())->toBe('/{record}/edit');
});

test('resource page classes are correctly referenced', function (): void {
    $pages = SimilarSiteCategoryResource::getPages();
    
    expect($pages)->toHaveKey('index');
    expect($pages)->toHaveKey('create');
    expect($pages)->toHaveKey('edit');
    
    expect(class_exists(ListSimilarSiteCategories::class))->toBeTrue();
    expect(class_exists(CreateSimilarSiteCategory::class))->toBeTrue();
    expect(class_exists(EditSimilarSiteCategory::class))->toBeTrue();
});

// Edge Cases and Error Handling Tests
test('form schema handles edge cases gracefully', function (): void {
    $form = new Form($this->resource);
    $formSchema = SimilarSiteCategoryResource::form($form);
    
    $schema = $formSchema->getSchema();
    
    foreach ($schema as $field) {
        expect($field->getName())
            ->not->toBeNull()
            ->toBeString();
    }
});

test('table schema handles edge cases gracefully', function (): void {
    $table = new Table($this->resource);
    $tableSchema = SimilarSiteCategoryResource::table($table);
    
    $columns = $tableSchema->getColumns();
    
    foreach ($columns as $column) {
        expect($column->getName())
            ->not->toBeNull()
            ->toBeString();
    }
});

test('form handles null or invalid input gracefully', function (): void {
    expect(function () {
        $form = new Form($this->resource);
        SimilarSiteCategoryResource::form($form);
    })->not->toThrow(TypeError::class);
});

test('table handles null or invalid input gracefully', function (): void {
    expect(function () {
        $table = new Table($this->resource);
        SimilarSiteCategoryResource::table($table);
    })->not->toThrow(TypeError::class);
});

test('resource handles empty model data appropriately', function (): void {
    $emptyModel = new SimilarSiteCategory();
    
    expect($emptyModel)->toBeInstanceOf(SimilarSiteCategory::class);
    expect($emptyModel->getFillable())->toContain('name');
    expect($emptyModel->getFillable())->toContain('description');
});

// Consistency and Immutability Tests
test('form can be instantiated multiple times without conflicts', function (): void {
    $form1 = SimilarSiteCategoryResource::form(new Form($this->resource));
    $form2 = SimilarSiteCategoryResource::form(new Form($this->resource));
    
    expect($form1)->toBeInstanceOf(Form::class);
    expect($form2)->toBeInstanceOf(Form::class);
    expect($form1)->not->toBe($form2);
});

test('table can be instantiated multiple times without conflicts', function (): void {
    $table1 = SimilarSiteCategoryResource::table(new Table($this->resource));
    $table2 = SimilarSiteCategoryResource::table(new Table($this->resource));
    
    expect($table1)->toBeInstanceOf(Table::class);
    expect($table2)->toBeInstanceOf(Table::class);
    expect($table1)->not->toBe($table2);
});

test('form schema consistency across multiple instantiations', function (): void {
    $form1 = SimilarSiteCategoryResource::form(new Form($this->resource));
    $form2 = SimilarSiteCategoryResource::form(new Form($this->resource));
    
    $schema1 = $form1->getSchema();
    $schema2 = $form2->getSchema();
    
    expect(count($schema1))->toBe(count($schema2));
    
    for ($i = 0; $i < count($schema1); $i++) {
        expect($schema1[$i]->getName())->toBe($schema2[$i]->getName());
        expect(get_class($schema1[$i]))->toBe(get_class($schema2[$i]));
    }
});

test('table schema consistency across multiple instantiations', function (): void {
    $table1 = SimilarSiteCategoryResource::table(new Table($this->resource));
    $table2 = SimilarSiteCategoryResource::table(new Table($this->resource));
    
    $columns1 = $table1->getColumns();
    $columns2 = $table2->getColumns();
    
    expect(count($columns1))->toBe(count($columns2));
    
    for ($i = 0; $i < count($columns1); $i++) {
        expect($columns1[$i]->getName())->toBe($columns2[$i]->getName());
        expect(get_class($columns1[$i]))->toBe(get_class($columns2[$i]));
    }
});

test('relations array maintains immutability', function (): void {
    $relations1 = SimilarSiteCategoryResource::getRelations();
    $relations2 = SimilarSiteCategoryResource::getRelations();
    
    expect($relations1)->toEqual($relations2);
    
    // Modify first array to test immutability
    $relations1[] = 'TestRelation';
    
    // Second call should still return original
    $relations3 = SimilarSiteCategoryResource::getRelations();
    expect($relations1)->not->toEqual($relations3);
    expect($relations2)->toEqual($relations3);
});

test('pages array maintains immutability', function (): void {
    $pages1 = SimilarSiteCategoryResource::getPages();
    $pages2 = SimilarSiteCategoryResource::getPages();
    
    expect($pages1)->toEqual($pages2);
    
    // Modify first array to test immutability
    $pages1['test'] = 'TestPage';
    
    // Second call should still return original
    $pages3 = SimilarSiteCategoryResource::getPages();
    expect($pages1)->not->toEqual($pages3);
    expect($pages2)->toEqual($pages3);
});

// Inheritance and Type Tests
test('resource inherits from filament resource correctly', function (): void {
    expect($this->resource)->toBeInstanceOf(Filament\Resources\Resource::class);
});

test('resource static methods return appropriate types', function (): void {
    expect(SimilarSiteCategoryResource::getModel())->toBeString();
    expect(SimilarSiteCategoryResource::getRelations())->toBeArray();
    expect(SimilarSiteCategoryResource::getPages())->toBeArray();
});

test('resource has all required methods for filament integration', function (): void {
    expect(method_exists(SimilarSiteCategoryResource::class, 'form'))->toBeTrue();
    expect(method_exists(SimilarSiteCategoryResource::class, 'table'))->toBeTrue();
    expect(method_exists(SimilarSiteCategoryResource::class, 'getRelations'))->toBeTrue();
    expect(method_exists(SimilarSiteCategoryResource::class, 'getPages'))->toBeTrue();
    expect(method_exists(SimilarSiteCategoryResource::class, 'getModel'))->toBeTrue();
});

// Field-Specific Configuration Tests
test('textarea field configuration for description', function (): void {
    $form = new Form($this->resource);
    $formSchema = SimilarSiteCategoryResource::form($form);
    $schema = $formSchema->getSchema();
    
    $descriptionField = $schema[1];
    expect($descriptionField->canSpanFullWidth())->toBeTrue();
});

test('form validation prevents submission with missing required fields', function (): void {
    $form = new Form($this->resource);
    $formSchema = SimilarSiteCategoryResource::form($form);
    $schema = $formSchema->getSchema();
    
    $nameField = $schema[0];
    expect($nameField->isRequired())->toBeTrue();
});

test('table columns support expected interactions', function (): void {
    $table = new Table($this->resource);
    $tableSchema = SimilarSiteCategoryResource::table($table);
    $columns = $tableSchema->getColumns();
    
    // Name column should support search
    expect($columns[0]->isSearchable())->toBeTrue();
    
    // Timestamp columns should support sorting
    expect($columns[1]->isSortable())->toBeTrue();
    expect($columns[2]->isSortable())->toBeTrue();
});

// Integration Tests
test('resource configuration works with real model instance', function (): void {
    // Test with actual model data
    $category = SimilarSiteCategory::factory()->create([
        'name' => 'Integration Test Category',
        'description' => 'This is a test description for integration testing'
    ]);
    
    expect($category->name)->toBe('Integration Test Category');
    expect($category->description)->toBe('This is a test description for integration testing');
    expect($category)->toBeInstanceOf(SimilarSiteCategory::class);
});

test('resource handles special characters in form fields', function (): void {
    $form = new Form($this->resource);
    $formSchema = SimilarSiteCategoryResource::form($form);
    
    // Test that form can handle special characters
    $specialCategory = SimilarSiteCategory::factory()->create([
        'name' => 'Test & Special <> Characters "quoted"',
        'description' => 'Description with émojis 🚀 and unicode ñáéíóú'
    ]);
    
    expect($specialCategory->name)->toContain('&');
    expect($specialCategory->description)->toContain('🚀');
});

test('resource handles maximum length field values', function (): void {
    // Test with very long strings to ensure proper handling
    $longName = str_repeat('A', 255); // Assuming max length of 255
    $longDescription = str_repeat('B', 1000); // Longer description
    
    $category = SimilarSiteCategory::factory()->create([
        'name' => $longName,
        'description' => $longDescription
    ]);
    
    expect(strlen($category->name))->toBe(255);
    expect(strlen($category->description))->toBe(1000);
});

test('resource configuration supports empty optional fields', function (): void {
    $categoryWithNullDescription = SimilarSiteCategory::factory()->create([
        'name' => 'Test Category',
        'description' => null
    ]);
    
    expect($categoryWithNullDescription->name)->toBe('Test Category');
    expect($categoryWithNullDescription->description)->toBeNull();
});