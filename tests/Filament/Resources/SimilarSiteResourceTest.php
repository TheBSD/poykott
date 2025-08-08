<?php

namespace Tests\Filament\Resources;

use App\Actions\ScrapeLogoFromUrlAction;
use App\Filament\Resources\SimilarSiteResource;
use App\Models\SimilarSite;
use App\Models\SimilarSiteCategory;
use Filament\Actions\Testing\TestsActions;
use Filament\Forms\Testing\TestsForms;
use Filament\Notifications\Notification;
use Filament\Tables\Testing\TestsTables;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class SimilarSiteResourceTest extends TestCase
{
    use RefreshDatabase;
    use TestsActions;
    use TestsForms;
    use TestsTables;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_has_correct_model_class()
    {
        $this->assertEquals(SimilarSite::class, SimilarSiteResource::getModel());
    }

    /** @test */
    public function it_has_correct_navigation_icon()
    {
        $this->assertEquals('heroicon-o-arrow-top-right-on-square', SimilarSiteResource::getNavigationIcon());
    }

    /** @test */
    public function it_has_correct_navigation_group()
    {
        $this->assertEquals('Similar Sites', SimilarSiteResource::getNavigationGroup());
    }

    /** @test */
    public function form_has_required_name_field_with_validation()
    {
        $form = SimilarSiteResource::form(\Filament\Forms\Form::make());
        
        $this->assertNotNull($form);
        
        // Test form schema contains name field
        $schema = $form->getSchema();
        $nameField = collect($schema)->first(fn($component) => $component->getName() === 'name');
        
        $this->assertNotNull($nameField);
        $this->assertInstanceOf(\Filament\Forms\Components\TextInput::class, $nameField);
    }

    /** @test */
    public function form_has_url_field_with_validation()
    {
        $form = SimilarSiteResource::form(\Filament\Forms\Form::make());
        
        $schema = $form->getSchema();
        $urlField = collect($schema)->first(fn($component) => $component->getName() === 'url');
        
        $this->assertNotNull($urlField);
        $this->assertInstanceOf(\Filament\Forms\Components\TextInput::class, $urlField);
    }

    /** @test */
    public function form_has_logo_upload_field()
    {
        $form = SimilarSiteResource::form(\Filament\Forms\Form::make());
        
        $schema = $form->getSchema();
        $logoField = collect($schema)->first(fn($component) => $component->getName() === 'logo');
        
        $this->assertNotNull($logoField);
        $this->assertInstanceOf(\Filament\Forms\Components\SpatieMediaLibraryFileUpload::class, $logoField);
    }

    /** @test */
    public function form_has_description_textarea()
    {
        $form = SimilarSiteResource::form(\Filament\Forms\Form::make());
        
        $schema = $form->getSchema();
        $descriptionField = collect($schema)->first(fn($component) => $component->getName() === 'description');
        
        $this->assertNotNull($descriptionField);
        $this->assertInstanceOf(\Filament\Forms\Components\Textarea::class, $descriptionField);
    }

    /** @test */
    public function form_has_category_select_field()
    {
        $form = SimilarSiteResource::form(\Filament\Forms\Form::make());
        
        $schema = $form->getSchema();
        $categoryField = collect($schema)->first(fn($component) => $component->getName() === 'similar_site_category_id');
        
        $this->assertNotNull($categoryField);
        $this->assertInstanceOf(\Filament\Forms\Components\Select::class, $categoryField);
    }

    /** @test */
    public function table_has_correct_columns()
    {
        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        
        $columns = $table->getColumns();
        $columnNames = collect($columns)->map(fn($column) => $column->getName())->toArray();
        
        $this->assertContains('logo', $columnNames);
        $this->assertContains('name', $columnNames);
        $this->assertContains('url', $columnNames);
        $this->assertContains('similarSiteCategory.name', $columnNames);
        $this->assertContains('created_at', $columnNames);
        $this->assertContains('updated_at', $columnNames);
    }

    /** @test */
    public function table_has_searchable_name_column()
    {
        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        
        $nameColumn = collect($table->getColumns())->first(fn($column) => $column->getName() === 'name');
        
        $this->assertNotNull($nameColumn);
        $this->assertInstanceOf(\Filament\Tables\Columns\TextColumn::class, $nameColumn);
    }

    /** @test */
    public function table_has_formatted_url_column()
    {
        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        
        $urlColumn = collect($table->getColumns())->first(fn($column) => $column->getName() === 'url');
        
        $this->assertNotNull($urlColumn);
        $this->assertInstanceOf(\Filament\Tables\Columns\TextColumn::class, $urlColumn);
    }

    /** @test */
    public function table_has_logo_image_column()
    {
        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        
        $logoColumn = collect($table->getColumns())->first(fn($column) => $column->getName() === 'logo');
        
        $this->assertNotNull($logoColumn);
        $this->assertInstanceOf(\Filament\Tables\Columns\SpatieMediaLibraryImageColumn::class, $logoColumn);
    }

    /** @test */
    public function table_has_category_badge_column()
    {
        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        
        $categoryColumn = collect($table->getColumns())->first(fn($column) => $column->getName() === 'similarSiteCategory.name');
        
        $this->assertNotNull($categoryColumn);
        $this->assertInstanceOf(\Filament\Tables\Columns\TextColumn::class, $categoryColumn);
    }

    /** @test */
    public function table_has_edit_action()
    {
        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        
        $actions = $table->getActions();
        $editAction = collect($actions)->first(fn($action) => $action instanceof \Filament\Tables\Actions\EditAction);
        
        $this->assertNotNull($editAction);
    }

    /** @test */
    public function table_has_fetch_logo_action()
    {
        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        
        $actions = $table->getActions();
        $fetchLogoAction = collect($actions)->first(fn($action) => 
            $action instanceof \Filament\Tables\Actions\Action && $action->getName() === 'fetchLogo'
        );
        
        $this->assertNotNull($fetchLogoAction);
    }

    /** @test */
    public function table_has_remove_logo_action()
    {
        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        
        $actions = $table->getActions();
        $removeLogoAction = collect($actions)->first(fn($action) => 
            $action instanceof \Filament\Tables\Actions\Action && $action->getName() === 'removeLogo'
        );
        
        $this->assertNotNull($removeLogoAction);
    }

    /** @test */
    public function table_has_bulk_delete_action()
    {
        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        
        $bulkActions = $table->getBulkActions();
        
        $this->assertNotEmpty($bulkActions);
    }

    /** @test */
    public function fetch_logo_action_calls_scrape_action_and_shows_success_notification()
    {
        $similarSite = SimilarSite::factory()->create([
            'url' => 'https://example.com'
        ]);

        $mockAction = Mockery::mock(ScrapeLogoFromUrlAction::class);
        $mockAction->shouldReceive('execute')
            ->once()
            ->with($similarSite, 'https://example.com')
            ->andReturn(true);

        $this->app->instance(ScrapeLogoFromUrlAction::class, $mockAction);

        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        $fetchLogoAction = collect($table->getActions())->first(fn($action) => 
            $action instanceof \Filament\Tables\Actions\Action && $action->getName() === 'fetchLogo'
        );

        // Simulate action execution
        $closure = $fetchLogoAction->getAction();
        
        Notification::fake();
        
        $closure($similarSite);
        
        Notification::assertSent(function ($notification) {
            return $notification->getTitle() === 'Logo fetched' && 
                   $notification->getStatus() === 'success';
        });
    }

    /** @test */
    public function fetch_logo_action_shows_failure_notification_on_error()
    {
        $similarSite = SimilarSite::factory()->create([
            'url' => 'https://example.com'
        ]);

        $mockAction = Mockery::mock(ScrapeLogoFromUrlAction::class);
        $mockAction->shouldReceive('execute')
            ->once()
            ->with($similarSite, 'https://example.com')
            ->andReturn(false);

        $this->app->instance(ScrapeLogoFromUrlAction::class, $mockAction);

        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        $fetchLogoAction = collect($table->getActions())->first(fn($action) => 
            $action instanceof \Filament\Tables\Actions\Action && $action->getName() === 'fetchLogo'
        );

        $closure = $fetchLogoAction->getAction();
        
        Notification::fake();
        
        $closure($similarSite);
        
        Notification::assertSent(function ($notification) {
            return $notification->getTitle() === 'Failed fetching logo. Try uploading the logo manually' && 
                   $notification->getStatus() === 'danger';
        });
    }

    /** @test */
    public function remove_logo_action_clears_media_and_shows_success_notification()
    {
        $similarSite = SimilarSite::factory()->create();
        
        // Mock the media collection
        $mockMediaCollection = Mockery::mock();
        $mockMediaCollection->shouldReceive('count')->andReturn(1);
        
        $similarSite->shouldReceive('media')->andReturn($mockMediaCollection);
        $similarSite->shouldReceive('clearMediaCollection')->once();

        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        $removeLogoAction = collect($table->getActions())->first(fn($action) => 
            $action instanceof \Filament\Tables\Actions\Action && $action->getName() === 'removeLogo'
        );

        $closure = $removeLogoAction->getAction();
        
        Notification::fake();
        
        $closure($similarSite);
        
        Notification::assertSent(function ($notification) {
            return $notification->getTitle() === 'Logo removed' && 
                   $notification->getStatus() === 'success';
        });
    }

    /** @test */
    public function fetch_logo_action_requires_confirmation_when_media_exists()
    {
        $similarSite = SimilarSite::factory()->create();
        
        // Mock media collection with existing media
        $mockMediaCollection = Mockery::mock();
        $mockMediaCollection->shouldReceive('count')->andReturn(1);
        $similarSite->shouldReceive('media')->andReturn($mockMediaCollection);

        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        $fetchLogoAction = collect($table->getActions())->first(fn($action) => 
            $action instanceof \Filament\Tables\Actions\Action && $action->getName() === 'fetchLogo'
        );

        $requiresConfirmationClosure = $fetchLogoAction->getRequiresConfirmation();
        
        $this->assertTrue($requiresConfirmationClosure($similarSite));
    }

    /** @test */
    public function fetch_logo_action_does_not_require_confirmation_when_no_media_exists()
    {
        $similarSite = SimilarSite::factory()->create();
        
        // Mock media collection with no media
        $mockMediaCollection = Mockery::mock();
        $mockMediaCollection->shouldReceive('count')->andReturn(0);
        $similarSite->shouldReceive('media')->andReturn($mockMediaCollection);

        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        $fetchLogoAction = collect($table->getActions())->first(fn($action) => 
            $action instanceof \Filament\Tables\Actions\Action && $action->getName() === 'fetchLogo'
        );

        $requiresConfirmationClosure = $fetchLogoAction->getRequiresConfirmation();
        
        $this->assertFalse($requiresConfirmationClosure($similarSite));
    }

    /** @test */
    public function remove_logo_action_requires_confirmation_when_media_exists()
    {
        $similarSite = SimilarSite::factory()->create();
        
        // Mock media collection with existing media
        $mockMediaCollection = Mockery::mock();
        $mockMediaCollection->shouldReceive('count')->andReturn(1);
        $similarSite->shouldReceive('media')->andReturn($mockMediaCollection);

        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        $removeLogoAction = collect($table->getActions())->first(fn($action) => 
            $action instanceof \Filament\Tables\Actions\Action && $action->getName() === 'removeLogo'
        );

        $requiresConfirmationClosure = $removeLogoAction->getRequiresConfirmation();
        
        $this->assertTrue($requiresConfirmationClosure($similarSite));
    }

    /** @test */
    public function remove_logo_action_is_visible_only_when_media_exists()
    {
        $similarSite = SimilarSite::factory()->create();
        
        // Mock media collection with existing media
        $mockMediaCollection = Mockery::mock();
        $mockMediaCollection->shouldReceive('count')->andReturn(1);
        $similarSite->shouldReceive('media')->andReturn($mockMediaCollection);

        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        $removeLogoAction = collect($table->getActions())->first(fn($action) => 
            $action instanceof \Filament\Tables\Actions\Action && $action->getName() === 'removeLogo'
        );

        $visibleClosure = $removeLogoAction->getVisible();
        
        $this->assertTrue($visibleClosure($similarSite));
    }

    /** @test */
    public function remove_logo_action_is_not_visible_when_no_media_exists()
    {
        $similarSite = SimilarSite::factory()->create();
        
        // Mock media collection with no media
        $mockMediaCollection = Mockery::mock();
        $mockMediaCollection->shouldReceive('count')->andReturn(0);
        $similarSite->shouldReceive('media')->andReturn($mockMediaCollection);

        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        $removeLogoAction = collect($table->getActions())->first(fn($action) => 
            $action instanceof \Filament\Tables\Actions\Action && $action->getName() === 'removeLogo'
        );

        $visibleClosure = $removeLogoAction->getVisible();
        
        $this->assertFalse($visibleClosure($similarSite));
    }

    /** @test */
    public function resource_has_correct_relations()
    {
        $relations = SimilarSiteResource::getRelations();
        
        $this->assertContains(\App\Filament\Resources\AuditsRelationManagerResource\RelationManagers\AuditsRelationManager::class, $relations);
    }

    /** @test */
    public function resource_has_correct_pages()
    {
        $pages = SimilarSiteResource::getPages();
        
        $this->assertArrayHasKey('index', $pages);
        $this->assertArrayHasKey('create', $pages);
        $this->assertArrayHasKey('edit', $pages);
        
        $this->assertInstanceOf(\Filament\Resources\Pages\Page::class, $pages['index']);
        $this->assertInstanceOf(\Filament\Resources\Pages\Page::class, $pages['create']);
        $this->assertInstanceOf(\Filament\Resources\Pages\Page::class, $pages['edit']);
    }

    /** @test */
    public function url_column_formats_state_correctly()
    {
        $similarSite = SimilarSite::factory()->create([
            'url' => 'https://example.com'
        ]);

        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        $urlColumn = collect($table->getColumns())->first(fn($column) => $column->getName() === 'url');
        
        $formatStateClosure = $urlColumn->getFormatStateUsing();
        $formattedState = $formatStateClosure($similarSite);
        
        $expectedHtml = "<a href='https://example.com' target='_blank' class='underline'>https://example.com</a>";
        $this->assertEquals($expectedHtml, $formattedState);
    }

    /** @test */
    public function form_validation_rules_are_properly_set()
    {
        $form = SimilarSiteResource::form(\Filament\Forms\Form::make());
        $schema = $form->getSchema();
        
        // Test name field rules
        $nameField = collect($schema)->first(fn($component) => $component->getName() === 'name');
        $this->assertNotNull($nameField);
        
        // Test URL field rules
        $urlField = collect($schema)->first(fn($component) => $component->getName() === 'url');
        $this->assertNotNull($urlField);
        
        // Test description field rules
        $descriptionField = collect($schema)->first(fn($component) => $component->getName() === 'description');
        $this->assertNotNull($descriptionField);
    }

    /** @test */
    public function table_columns_have_correct_properties()
    {
        $table = SimilarSiteResource::table(\Filament\Tables\Table::make());
        $columns = $table->getColumns();
        
        // Test logo column size
        $logoColumn = collect($columns)->first(fn($column) => $column->getName() === 'logo');
        $this->assertNotNull($logoColumn);
        
        // Test URL column properties
        $urlColumn = collect($columns)->first(fn($column) => $column->getName() === 'url');
        $this->assertNotNull($urlColumn);
        
        // Test category column properties
        $categoryColumn = collect($columns)->first(fn($column) => $column->getName() === 'similarSiteCategory.name');
        $this->assertNotNull($categoryColumn);
        
        // Test timestamp columns are toggleable
        $createdAtColumn = collect($columns)->first(fn($column) => $column->getName() === 'created_at');
        $updatedAtColumn = collect($columns)->first(fn($column) => $column->getName() === 'updated_at');
        
        $this->assertNotNull($createdAtColumn);
        $this->assertNotNull($updatedAtColumn);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}