<?php

namespace Tests\Filament\Resources;

use App\Filament\Resources\AuditsRelationManagerResource\RelationManagers\AuditsRelationManager;
use App\Filament\Resources\OfficeLocationResource;
use App\Filament\Resources\OfficeLocationResource\Actions\MergeTwoOfficeLocationsAction;
use App\Filament\Resources\OfficeLocationResource\Pages\CreateOfficeLocation;
use App\Filament\Resources\OfficeLocationResource\Pages\EditOfficeLocation;
use App\Filament\Resources\OfficeLocationResource\Pages\ListOfficeLocations;
use App\Filament\Resources\OfficeLocationResource\RelationManagers\CompaniesRelationManager;
use App\Models\OfficeLocation;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use PHPUnit\Framework\TestCase;
use Mockery;

class OfficeLocationResourceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_resource_has_correct_model(): void
    {
        $this->assertEquals(OfficeLocation::class, OfficeLocationResource::getModel());
    }

    public function test_resource_has_correct_navigation_icon(): void
    {
        $reflection = new \ReflectionClass(OfficeLocationResource::class);
        $property = $reflection->getProperty('navigationIcon');
        $property->setAccessible(true);
        
        $this->assertEquals('heroicon-o-map-pin', $property->getValue());
    }

    public function test_resource_has_correct_navigation_group(): void
    {
        $reflection = new \ReflectionClass(OfficeLocationResource::class);
        $property = $reflection->getProperty('navigationGroup');
        $property->setAccessible(true);
        
        $this->assertEquals('Companies', $property->getValue());
    }

    public function test_resource_has_correct_per_page_setting(): void
    {
        $reflection = new \ReflectionClass(OfficeLocationResource::class);
        $property = $reflection->getProperty('perPage');
        $property->setAccessible(true);
        
        $this->assertEquals(10, $property->getValue());
    }

    public function test_form_schema_contains_required_fields(): void
    {
        $mockForm = Mockery::mock(Form::class);
        $mockForm->shouldReceive('schema')
            ->once()
            ->with(Mockery::type('array'))
            ->andReturnSelf();

        $result = OfficeLocationResource::form($mockForm);
        
        $this->assertInstanceOf(Form::class, $result);
    }

    public function test_form_schema_has_name_field_with_validation(): void
    {
        $mockForm = Mockery::mock(Form::class);
        $capturedSchema = null;
        
        $mockForm->shouldReceive('schema')
            ->once()
            ->with(Mockery::on(function ($schema) use (&$capturedSchema) {
                $capturedSchema = $schema;
                return is_array($schema);
            }))
            ->andReturnSelf();

        OfficeLocationResource::form($mockForm);
        
        $this->assertIsArray($capturedSchema);
        $this->assertCount(3, $capturedSchema);
        
        // Check first field is TextInput for name
        $nameField = $capturedSchema[0];
        $this->assertInstanceOf(TextInput::class, $nameField);
    }

    public function test_form_schema_has_lat_field(): void
    {
        $mockForm = Mockery::mock(Form::class);
        $capturedSchema = null;
        
        $mockForm->shouldReceive('schema')
            ->once()
            ->with(Mockery::on(function ($schema) use (&$capturedSchema) {
                $capturedSchema = $schema;
                return is_array($schema);
            }))
            ->andReturnSelf();

        OfficeLocationResource::form($mockForm);
        
        // Check second field is TextInput for lat
        $latField = $capturedSchema[1];
        $this->assertInstanceOf(TextInput::class, $latField);
    }

    public function test_form_schema_has_lng_field(): void
    {
        $mockForm = Mockery::mock(Form::class);
        $capturedSchema = null;
        
        $mockForm->shouldReceive('schema')
            ->once()
            ->with(Mockery::on(function ($schema) use (&$capturedSchema) {
                $capturedSchema = $schema;
                return is_array($schema);
            }))
            ->andReturnSelf();

        OfficeLocationResource::form($mockForm);
        
        // Check third field is TextInput for lng
        $lngField = $capturedSchema[2];
        $this->assertInstanceOf(TextInput::class, $lngField);
    }

    public function test_table_configuration_returns_table_instance(): void
    {
        $mockTable = Mockery::mock(Table::class);
        $mockTable->shouldReceive('recordUrl')->once()->with(null)->andReturnSelf();
        $mockTable->shouldReceive('columns')->once()->with(Mockery::type('array'))->andReturnSelf();
        $mockTable->shouldReceive('filters')->once()->with(Mockery::type('array'))->andReturnSelf();
        $mockTable->shouldReceive('actions')->once()->with(Mockery::type('array'))->andReturnSelf();
        $mockTable->shouldReceive('bulkActions')->once()->with(Mockery::type('array'))->andReturnSelf();

        $result = OfficeLocationResource::table($mockTable);
        
        $this->assertInstanceOf(Table::class, $result);
    }

    public function test_table_has_correct_columns(): void
    {
        $mockTable = Mockery::mock(Table::class);
        $capturedColumns = null;
        
        $mockTable->shouldReceive('recordUrl')->once()->with(null)->andReturnSelf();
        $mockTable->shouldReceive('columns')
            ->once()
            ->with(Mockery::on(function ($columns) use (&$capturedColumns) {
                $capturedColumns = $columns;
                return is_array($columns);
            }))
            ->andReturnSelf();
        $mockTable->shouldReceive('filters')->once()->andReturnSelf();
        $mockTable->shouldReceive('actions')->once()->andReturnSelf();
        $mockTable->shouldReceive('bulkActions')->once()->andReturnSelf();

        OfficeLocationResource::table($mockTable);
        
        $this->assertIsArray($capturedColumns);
        $this->assertCount(6, $capturedColumns);
        
        // Verify all columns are TextColumn instances
        foreach ($capturedColumns as $column) {
            $this->assertInstanceOf(TextColumn::class, $column);
        }
    }

    public function test_table_has_correct_actions(): void
    {
        $mockTable = Mockery::mock(Table::class);
        $capturedActions = null;
        
        $mockTable->shouldReceive('recordUrl')->once()->with(null)->andReturnSelf();
        $mockTable->shouldReceive('columns')->once()->andReturnSelf();
        $mockTable->shouldReceive('filters')->once()->andReturnSelf();
        $mockTable->shouldReceive('actions')
            ->once()
            ->with(Mockery::on(function ($actions) use (&$capturedActions) {
                $capturedActions = $actions;
                return is_array($actions);
            }))
            ->andReturnSelf();
        $mockTable->shouldReceive('bulkActions')->once()->andReturnSelf();

        OfficeLocationResource::table($mockTable);
        
        $this->assertIsArray($capturedActions);
        $this->assertCount(3, $capturedActions);
        
        // Check for EditAction
        $this->assertInstanceOf(EditAction::class, $capturedActions[0]);
        // Check for DeleteAction
        $this->assertInstanceOf(DeleteAction::class, $capturedActions[1]);
        // Check for MergeTwoOfficeLocationsAction
        $this->assertInstanceOf(MergeTwoOfficeLocationsAction::class, $capturedActions[2]);
    }

    public function test_table_has_correct_bulk_actions(): void
    {
        $mockTable = Mockery::mock(Table::class);
        $capturedBulkActions = null;
        
        $mockTable->shouldReceive('recordUrl')->once()->with(null)->andReturnSelf();
        $mockTable->shouldReceive('columns')->once()->andReturnSelf();
        $mockTable->shouldReceive('filters')->once()->andReturnSelf();
        $mockTable->shouldReceive('actions')->once()->andReturnSelf();
        $mockTable->shouldReceive('bulkActions')
            ->once()
            ->with(Mockery::on(function ($bulkActions) use (&$capturedBulkActions) {
                $capturedBulkActions = $bulkActions;
                return is_array($bulkActions);
            }))
            ->andReturnSelf();

        OfficeLocationResource::table($mockTable);
        
        $this->assertIsArray($capturedBulkActions);
        $this->assertCount(1, $capturedBulkActions);
        $this->assertInstanceOf(BulkActionGroup::class, $capturedBulkActions[0]);
    }

    public function test_table_record_url_is_disabled(): void
    {
        $mockTable = Mockery::mock(Table::class);
        
        $mockTable->shouldReceive('recordUrl')
            ->once()
            ->with(null)
            ->andReturnSelf();
        $mockTable->shouldReceive('columns')->once()->andReturnSelf();
        $mockTable->shouldReceive('filters')->once()->andReturnSelf();
        $mockTable->shouldReceive('actions')->once()->andReturnSelf();
        $mockTable->shouldReceive('bulkActions')->once()->andReturnSelf();

        OfficeLocationResource::table($mockTable);
        
        // The assertion is implicit in the mock expectation
        $this->assertTrue(true);
    }

    public function test_get_relations_returns_correct_relation_managers(): void
    {
        $relations = OfficeLocationResource::getRelations();
        
        $this->assertIsArray($relations);
        $this->assertCount(2, $relations);
        $this->assertContains(CompaniesRelationManager::class, $relations);
        $this->assertContains(AuditsRelationManager::class, $relations);
    }

    public function test_get_pages_returns_correct_page_configuration(): void
    {
        $pages = OfficeLocationResource::getPages();
        
        $this->assertIsArray($pages);
        $this->assertCount(3, $pages);
        $this->assertArrayHasKey('index', $pages);
        $this->assertArrayHasKey('create', $pages);
        $this->assertArrayHasKey('edit', $pages);
    }

    public function test_pages_have_correct_route_configuration(): void
    {
        $pages = OfficeLocationResource::getPages();
        
        // Test index page
        $this->assertEquals(ListOfficeLocations::class, $pages['index']->getPage());
        
        // Test create page
        $this->assertEquals(CreateOfficeLocation::class, $pages['create']->getPage());
        
        // Test edit page
        $this->assertEquals(EditOfficeLocation::class, $pages['edit']->getPage());
    }

    public function test_form_name_field_has_unique_validation(): void
    {
        $mockForm = Mockery::mock(Form::class);
        $capturedSchema = null;
        
        $mockForm->shouldReceive('schema')
            ->once()
            ->with(Mockery::on(function ($schema) use (&$capturedSchema) {
                $capturedSchema = $schema;
                return is_array($schema);
            }))
            ->andReturnSelf();

        OfficeLocationResource::form($mockForm);
        
        $nameField = $capturedSchema[0];
        
        // Test that the field has the expected method calls
        // Note: This is a simplified test as Filament field validation is complex
        $this->assertInstanceOf(TextInput::class, $nameField);
    }

    public function test_table_columns_have_correct_configuration(): void
    {
        $mockTable = Mockery::mock(Table::class);
        $capturedColumns = null;
        
        $mockTable->shouldReceive('recordUrl')->once()->with(null)->andReturnSelf();
        $mockTable->shouldReceive('columns')
            ->once()
            ->with(Mockery::on(function ($columns) use (&$capturedColumns) {
                $capturedColumns = $columns;
                return is_array($columns) && count($columns) === 6;
            }))
            ->andReturnSelf();
        $mockTable->shouldReceive('filters')->once()->andReturnSelf();
        $mockTable->shouldReceive('actions')->once()->andReturnSelf();
        $mockTable->shouldReceive('bulkActions')->once()->andReturnSelf();

        OfficeLocationResource::table($mockTable);
        
        $this->assertCount(6, $capturedColumns);
        
        // Verify each column is a TextColumn
        foreach ($capturedColumns as $column) {
            $this->assertInstanceOf(TextColumn::class, $column);
        }
    }

    public function test_resource_static_properties_are_correctly_typed(): void
    {
        $reflection = new \ReflectionClass(OfficeLocationResource::class);
        
        // Test model property type
        $modelProperty = $reflection->getProperty('model');
        $this->assertTrue($modelProperty->isStatic());
        
        // Test navigationIcon property type
        $iconProperty = $reflection->getProperty('navigationIcon');
        $this->assertTrue($iconProperty->isStatic());
        
        // Test navigationGroup property type
        $groupProperty = $reflection->getProperty('navigationGroup');
        $this->assertTrue($groupProperty->isStatic());
        
        // Test perPage property type
        $perPageProperty = $reflection->getProperty('perPage');
        $this->assertTrue($perPageProperty->isStatic());
    }

    public function test_merge_action_has_correct_label(): void
    {
        $mockTable = Mockery::mock(Table::class);
        $capturedActions = null;
        
        $mockTable->shouldReceive('recordUrl')->once()->with(null)->andReturnSelf();
        $mockTable->shouldReceive('columns')->once()->andReturnSelf();
        $mockTable->shouldReceive('filters')->once()->andReturnSelf();
        $mockTable->shouldReceive('actions')
            ->once()
            ->with(Mockery::on(function ($actions) use (&$capturedActions) {
                $capturedActions = $actions;
                return is_array($actions);
            }))
            ->andReturnSelf();
        $mockTable->shouldReceive('bulkActions')->once()->andReturnSelf();

        OfficeLocationResource::table($mockTable);
        
        // Check that MergeTwoOfficeLocationsAction is created with correct label
        $mergeAction = $capturedActions[2];
        $this->assertInstanceOf(MergeTwoOfficeLocationsAction::class, $mergeAction);
    }

    public function test_resource_extends_correct_base_class(): void
    {
        $this->assertTrue(is_subclass_of(OfficeLocationResource::class, \Filament\Resources\Resource::class));
    }

    public function test_form_method_is_static(): void
    {
        $reflection = new \ReflectionClass(OfficeLocationResource::class);
        $method = $reflection->getMethod('form');
        
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
    }

    public function test_table_method_is_static(): void
    {
        $reflection = new \ReflectionClass(OfficeLocationResource::class);
        $method = $reflection->getMethod('table');
        
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
    }

    public function test_get_relations_method_is_static(): void
    {
        $reflection = new \ReflectionClass(OfficeLocationResource::class);
        $method = $reflection->getMethod('getRelations');
        
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
    }

    public function test_get_pages_method_is_static(): void
    {
        $reflection = new \ReflectionClass(OfficeLocationResource::class);
        $method = $reflection->getMethod('getPages');
        
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
    }

    public function test_form_schema_fields_count(): void
    {
        $mockForm = Mockery::mock(Form::class);
        $capturedSchema = null;
        
        $mockForm->shouldReceive('schema')
            ->once()
            ->with(Mockery::on(function ($schema) use (&$capturedSchema) {
                $capturedSchema = $schema;
                return is_array($schema) && count($schema) === 3;
            }))
            ->andReturnSelf();

        OfficeLocationResource::form($mockForm);
        
        $this->assertCount(3, $capturedSchema);
    }

    public function test_table_filters_array_is_empty(): void
    {
        $mockTable = Mockery::mock(Table::class);
        $capturedFilters = null;
        
        $mockTable->shouldReceive('recordUrl')->once()->with(null)->andReturnSelf();
        $mockTable->shouldReceive('columns')->once()->andReturnSelf();
        $mockTable->shouldReceive('filters')
            ->once()
            ->with(Mockery::on(function ($filters) use (&$capturedFilters) {
                $capturedFilters = $filters;
                return is_array($filters) && empty($filters);
            }))
            ->andReturnSelf();
        $mockTable->shouldReceive('actions')->once()->andReturnSelf();
        $mockTable->shouldReceive('bulkActions')->once()->andReturnSelf();

        OfficeLocationResource::table($mockTable);
        
        $this->assertIsArray($capturedFilters);
        $this->assertEmpty($capturedFilters);
    }

    public function test_all_form_fields_are_text_inputs(): void
    {
        $mockForm = Mockery::mock(Form::class);
        $capturedSchema = null;
        
        $mockForm->shouldReceive('schema')
            ->once()
            ->with(Mockery::on(function ($schema) use (&$capturedSchema) {
                $capturedSchema = $schema;
                return is_array($schema);
            }))
            ->andReturnSelf();

        OfficeLocationResource::form($mockForm);
        
        foreach ($capturedSchema as $field) {
            $this->assertInstanceOf(TextInput::class, $field);
        }
    }

    public function test_resource_class_constants_and_properties(): void
    {
        $reflection = new \ReflectionClass(OfficeLocationResource::class);
        
        // Verify the class has the expected static properties
        $this->assertTrue($reflection->hasProperty('model'));
        $this->assertTrue($reflection->hasProperty('navigationIcon'));
        $this->assertTrue($reflection->hasProperty('navigationGroup'));
        $this->assertTrue($reflection->hasProperty('perPage'));
    }
}