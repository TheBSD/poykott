<?php

namespace Tests\Filament\Resources;

use App\Filament\Resources\AuditsRelationManagerResource\RelationManagers\AuditsRelationManager;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    /** @test */
    public function it_has_correct_model_property()
    {
        $this->assertEquals(User::class, UserResource::getModel());
    }

    /** @test */
    public function it_has_correct_navigation_icon()
    {
        $reflection = new \ReflectionClass(UserResource::class);
        $property = $reflection->getProperty('navigationIcon');
        $property->setAccessible(true);
        
        $this->assertEquals('heroicon-o-user', $property->getValue());
    }

    /** @test */
    public function it_has_correct_navigation_group()
    {
        $reflection = new \ReflectionClass(UserResource::class);
        $property = $reflection->getProperty('navigationGroup');
        $property->setAccessible(true);
        
        $this->assertEquals('Internals', $property->getValue());
    }

    /** @test */
    public function form_method_returns_correct_form_schema()
    {
        $form = $this->createMock(Form::class);
        $form->expects($this->once())
            ->method('schema')
            ->with($this->callback(function ($schema) {
                // Verify we have 4 form components
                $this->assertCount(4, $schema);
                
                // Verify name field
                $this->assertInstanceOf(TextInput::class, $schema[0]);
                $this->assertEquals('name', $schema[0]->getName());
                $this->assertTrue($schema[0]->isRequired());
                
                // Verify email field
                $this->assertInstanceOf(TextInput::class, $schema[1]);
                $this->assertEquals('email', $schema[1]->getName());
                $this->assertTrue($schema[1]->isRequired());
                
                // Verify password field
                $this->assertInstanceOf(TextInput::class, $schema[2]);
                $this->assertEquals('password', $schema[2]->getName());
                $this->assertTrue($schema[2]->isRequired());
                
                // Verify email_verified_at field
                $this->assertInstanceOf(DateTimePicker::class, $schema[3]);
                $this->assertEquals('email_verified_at', $schema[3]->getName());
                
                return true;
            }))
            ->willReturnSelf();

        UserResource::form($form);
    }

    /** @test */
    public function table_method_returns_correct_table_configuration()
    {
        $table = $this->createMock(Table::class);
        
        // Mock the fluent interface
        $table->expects($this->once())
            ->method('columns')
            ->with($this->callback(function ($columns) {
                $this->assertCount(5, $columns);
                
                // Verify name column
                $this->assertInstanceOf(TextColumn::class, $columns[0]);
                $this->assertEquals('name', $columns[0]->getName());
                
                // Verify email column
                $this->assertInstanceOf(TextColumn::class, $columns[1]);
                $this->assertEquals('email', $columns[1]->getName());
                
                // Verify email_verified_at column
                $this->assertInstanceOf(IconColumn::class, $columns[2]);
                $this->assertEquals('email_verified_at', $columns[2]->getName());
                
                // Verify created_at column
                $this->assertInstanceOf(TextColumn::class, $columns[3]);
                $this->assertEquals('created_at', $columns[3]->getName());
                
                // Verify updated_at column
                $this->assertInstanceOf(TextColumn::class, $columns[4]);
                $this->assertEquals('updated_at', $columns[4]->getName());
                
                return true;
            }))
            ->willReturnSelf();

        $table->expects($this->once())
            ->method('filters')
            ->with([])
            ->willReturnSelf();

        $table->expects($this->once())
            ->method('actions')
            ->with($this->callback(function ($actions) {
                $this->assertCount(2, $actions);
                $this->assertInstanceOf(EditAction::class, $actions[0]);
                $this->assertInstanceOf(DeleteAction::class, $actions[1]);
                return true;
            }))
            ->willReturnSelf();

        $table->expects($this->once())
            ->method('bulkActions')
            ->with($this->callback(function ($bulkActions) {
                $this->assertCount(1, $bulkActions);
                $this->assertInstanceOf(BulkActionGroup::class, $bulkActions[0]);
                return true;
            }))
            ->willReturnSelf();

        UserResource::table($table);
    }

    /** @test */
    public function delete_action_is_visible_for_non_admin_users()
    {
        $table = $this->createMock(Table::class);
        $table->method('columns')->willReturnSelf();
        $table->method('filters')->willReturnSelf();
        $table->method('bulkActions')->willReturnSelf();
        
        $table->expects($this->once())
            ->method('actions')
            ->with($this->callback(function ($actions) {
                /** @var DeleteAction $deleteAction */
                $deleteAction = $actions[1];
                
                // Test visibility logic - should be visible for non-admin user (id !== 1)
                $regularUser = User::factory()->create(['id' => 2]);
                $adminUser = User::factory()->create(['id' => 1]);
                
                $visibilityCallback = $deleteAction->getVisibility();
                if (is_callable($visibilityCallback)) {
                    $this->assertTrue($visibilityCallback($regularUser));
                    $this->assertFalse($visibilityCallback($adminUser));
                }
                
                return true;
            }))
            ->willReturnSelf();

        UserResource::table($table);
    }

    /** @test */
    public function get_relations_returns_correct_relation_managers()
    {
        $relations = UserResource::getRelations();
        
        $this->assertIsArray($relations);
        $this->assertCount(1, $relations);
        $this->assertEquals(AuditsRelationManager::class, $relations[0]);
    }

    /** @test */
    public function get_pages_returns_correct_page_configuration()
    {
        $pages = UserResource::getPages();
        
        $this->assertIsArray($pages);
        $this->assertCount(3, $pages);
        
        // Verify index page
        $this->assertArrayHasKey('index', $pages);
        $this->assertEquals(ListUsers::class, $pages['index']);
        
        // Verify create page
        $this->assertArrayHasKey('create', $pages);
        $this->assertEquals(CreateUser::class, $pages['create']);
        
        // Verify edit page
        $this->assertArrayHasKey('edit', $pages);
        $this->assertEquals(EditUser::class, $pages['edit']);
    }

    /** @test */
    public function form_validates_required_fields()
    {
        $form = UserResource::form(new Form());
        $schema = $form->getSchema();
        
        // Check that required fields are properly configured
        $nameField = collect($schema)->first(fn($field) => $field->getName() === 'name');
        $this->assertTrue($nameField->isRequired());
        
        $emailField = collect($schema)->first(fn($field) => $field->getName() === 'email');
        $this->assertTrue($emailField->isRequired());
        
        $passwordField = collect($schema)->first(fn($field) => $field->getName() === 'password');
        $this->assertTrue($passwordField->isRequired());
    }

    /** @test */
    public function form_has_email_validation()
    {
        $form = UserResource::form(new Form());
        $schema = $form->getSchema();
        
        $emailField = collect($schema)->first(fn($field) => $field->getName() === 'email');
        $this->assertInstanceOf(TextInput::class, $emailField);
        
        // Email field should have email validation
        $rules = $emailField->getValidationRules();
        $this->assertContains('email', $rules);
    }

    /** @test */
    public function form_has_password_field_configured()
    {
        $form = UserResource::form(new Form());
        $schema = $form->getSchema();
        
        $passwordField = collect($schema)->first(fn($field) => $field->getName() === 'password');
        $this->assertInstanceOf(TextInput::class, $passwordField);
        $this->assertTrue($passwordField->isPassword());
    }

    /** @test */
    public function table_columns_are_properly_configured()
    {
        $table = UserResource::table(new Table());
        $columns = $table->getColumns();
        
        // Name column should be searchable
        $nameColumn = collect($columns)->first(fn($col) => $col->getName() === 'name');
        $this->assertTrue($nameColumn->isSearchable());
        
        // Email column should be searchable
        $emailColumn = collect($columns)->first(fn($col) => $col->getName() === 'email');
        $this->assertTrue($emailColumn->isSearchable());
        
        // Email verified column should be sortable and boolean
        $verifiedColumn = collect($columns)->first(fn($col) => $col->getName() === 'email_verified_at');
        $this->assertInstanceOf(IconColumn::class, $verifiedColumn);
        $this->assertTrue($verifiedColumn->isSortable());
        
        // Created at column should be sortable and toggleable
        $createdAtColumn = collect($columns)->first(fn($col) => $col->getName() === 'created_at');
        $this->assertTrue($createdAtColumn->isSortable());
        $this->assertTrue($createdAtColumn->isToggledHiddenByDefault());
        
        // Updated at column should be sortable and toggleable
        $updatedAtColumn = collect($columns)->first(fn($col) => $col->getName() === 'updated_at');
        $this->assertTrue($updatedAtColumn->isSortable());
        $this->assertTrue($updatedAtColumn->isToggledHiddenByDefault());
    }

    /** @test */
    public function bulk_actions_include_delete_action()
    {
        $table = UserResource::table(new Table());
        $bulkActions = $table->getBulkActions();
        
        $this->assertCount(1, $bulkActions);
        $this->assertInstanceOf(BulkActionGroup::class, $bulkActions[0]);
        
        // Check that the bulk action group contains delete action
        $actions = $bulkActions[0]->getActions();
        $this->assertCount(1, $actions);
        $this->assertInstanceOf(DeleteBulkAction::class, $actions[0]);
    }

    /** @test */
    public function resource_extends_base_resource_class()
    {
        $this->assertInstanceOf(Resource::class, new UserResource());
    }

    /** @test */
    public function page_routes_are_correctly_configured()
    {
        $pages = UserResource::getPages();
        
        // Verify route patterns match expected Filament conventions
        $this->assertEquals('/', $pages['index']->getRoute());
        $this->assertEquals('/create', $pages['create']->getRoute());
        $this->assertEquals('/{record}/edit', $pages['edit']->getRoute());
    }

    /** @test */
    public function email_verified_at_field_is_optional()
    {
        $form = UserResource::form(new Form());
        $schema = $form->getSchema();
        
        $emailVerifiedField = collect($schema)->first(fn($field) => $field->getName() === 'email_verified_at');
        $this->assertInstanceOf(DateTimePicker::class, $emailVerifiedField);
        $this->assertFalse($emailVerifiedField->isRequired());
    }

    /** @test */
    public function form_schema_has_correct_field_count()
    {
        $form = UserResource::form(new Form());
        $schema = $form->getSchema();
        
        $this->assertCount(4, $schema);
    }

    /** @test */
    public function table_has_no_filters_configured()
    {
        $table = UserResource::table(new Table());
        $filters = $table->getFilters();
        
        $this->assertEmpty($filters);
    }

    /** @test */
    public function delete_action_visibility_logic_works_correctly()
    {
        // Create users with different IDs
        $adminUser = User::factory()->create(['id' => 1]);
        $regularUser = User::factory()->create(['id' => 2]);
        
        $table = UserResource::table(new Table());
        $actions = $table->getActions();
        
        /** @var DeleteAction $deleteAction */
        $deleteAction = collect($actions)->first(fn($action) => $action instanceof DeleteAction);
        
        $visibilityCallback = $deleteAction->getVisibility();
        
        // Admin user (ID = 1) should not see delete action
        $this->assertFalse($visibilityCallback($adminUser));
        
        // Regular user should see delete action
        $this->assertTrue($visibilityCallback($regularUser));
    }

    /** @test */
    public function resource_has_edit_and_delete_actions()
    {
        $table = UserResource::table(new Table());
        $actions = $table->getActions();
        
        $this->assertCount(2, $actions);
        
        $editAction = collect($actions)->first(fn($action) => $action instanceof EditAction);
        $deleteAction = collect($actions)->first(fn($action) => $action instanceof DeleteAction);
        
        $this->assertNotNull($editAction);
        $this->assertNotNull($deleteAction);
    }

    /** @test */
    public function all_required_imports_are_present()
    {
        // This test ensures all necessary classes are imported
        $this->assertTrue(class_exists(UserResource::class));
        $this->assertTrue(class_exists(User::class));
        $this->assertTrue(class_exists(AuditsRelationManager::class));
        $this->assertTrue(class_exists(CreateUser::class));
        $this->assertTrue(class_exists(EditUser::class));
        $this->assertTrue(class_exists(ListUsers::class));
    }
}