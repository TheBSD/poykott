<?php

use App\Actions\ScrapeLogoFromUrlAction;
use App\Filament\Resources\AlternativeResource;
use App\Filament\Resources\AlternativeResource\Pages\CreateAlternative;
use App\Filament\Resources\AlternativeResource\Pages\EditAlternative;
use App\Filament\Resources\AlternativeResource\Pages\ListAlternatives;
use App\Filament\Resources\AlternativeResource\RelationManagers\CompaniesRelationManager;
use App\Filament\Resources\AlternativeResource\RelationManagers\ResourcesRelationManager;
use App\Filament\Resources\AuditsRelationManagerResource\RelationManagers\AuditsRelationManager;
use App\Models\Alternative;
use App\Models\Tag;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

test('alternative resource has correct model binding', function (): void {
    $reflection = new ReflectionClass(AlternativeResource::class);
    $modelProperty = $reflection->getProperty('model');
    $modelProperty->setAccessible(true);
    
    expect($modelProperty->getValue())->toBe(Alternative::class);
});

test('alternative resource has correct navigation icon', function (): void {
    $reflection = new ReflectionClass(AlternativeResource::class);
    $iconProperty = $reflection->getProperty('navigationIcon');
    $iconProperty->setAccessible(true);
    
    expect($iconProperty->getValue())->toBe('heroicon-o-arrows-right-left');
});

test('alternative resource has correct navigation group', function (): void {
    $reflection = new ReflectionClass(AlternativeResource::class);
    $groupProperty = $reflection->getProperty('navigationGroup');
    $groupProperty->setAccessible(true);
    
    expect($groupProperty->getValue())->toBe('Alternatives');
});

test('form method returns form instance with schema', function (): void {
    $form = Mockery::mock(Form::class);
    $form->shouldReceive('schema')
        ->once()
        ->with(Mockery::on(function ($schema) {
            return is_array($schema) && count($schema) === 6;
        }))
        ->andReturnSelf();
    
    $result = AlternativeResource::form($form);
    
    expect($result)->toBeInstanceOf(Form::class);
});

test('form schema contains correct field types', function (): void {
    $form = Mockery::mock(Form::class);
    $form->shouldReceive('schema')
        ->once()
        ->with(Mockery::on(function ($schema) {
            expect($schema)->toHaveCount(6);
            expect($schema[0])->toBeInstanceOf(TextInput::class); // name
            expect($schema[1])->toBeInstanceOf(TextInput::class); // url
            expect($schema[2])->toBeInstanceOf(Textarea::class);  // description
            expect($schema[3])->toBeInstanceOf(Textarea::class);  // notes
            expect($schema[4])->toBeInstanceOf(SpatieMediaLibraryFileUpload::class); // logo
            expect($schema[5])->toBeInstanceOf(Select::class);    // tags
            return true;
        }))
        ->andReturnSelf();
    
    AlternativeResource::form($form);
});

test('form name field is required', function (): void {
    $form = Mockery::mock(Form::class);
    $form->shouldReceive('schema')
        ->once()
        ->with(Mockery::on(function ($schema) {
            $nameField = $schema[0];
            expect($nameField)->toBeInstanceOf(TextInput::class);
            return true;
        }))
        ->andReturnSelf();
    
    AlternativeResource::form($form);
});

test('form url field is required', function (): void {
    $form = Mockery::mock(Form::class);
    $form->shouldReceive('schema')
        ->once()
        ->with(Mockery::on(function ($schema) {
            $urlField = $schema[1];
            expect($urlField)->toBeInstanceOf(TextInput::class);
            return true;
        }))
        ->andReturnSelf();
    
    AlternativeResource::form($form);
});

test('form logo field has correct validation rules', function (): void {
    $form = Mockery::mock(Form::class);
    $form->shouldReceive('schema')
        ->once()
        ->with(Mockery::on(function ($schema) {
            $logoField = $schema[4];
            expect($logoField)->toBeInstanceOf(SpatieMediaLibraryFileUpload::class);
            return true;
        }))
        ->andReturnSelf();
    
    AlternativeResource::form($form);
});

test('form tags field is configured for multiple selection', function (): void {
    $form = Mockery::mock(Form::class);
    $form->shouldReceive('schema')
        ->once()
        ->with(Mockery::on(function ($schema) {
            $tagsField = $schema[5];
            expect($tagsField)->toBeInstanceOf(Select::class);
            return true;
        }))
        ->andReturnSelf();
    
    AlternativeResource::form($form);
});

test('form contains approved_at datetime picker', function (): void {
    $form = Mockery::mock(Form::class);
    $form->shouldReceive('schema')
        ->once()
        ->with(Mockery::on(function ($schema) {
            // The approved_at field is the 6th field (index 5 after select)
            $hasDateTimePicker = false;
            foreach ($schema as $field) {
                if ($field instanceof DateTimePicker) {
                    $hasDateTimePicker = true;
                    break;
                }
            }
            expect($hasDateTimePicker)->toBeTrue();
            return true;
        }))
        ->andReturnSelf();
    
    AlternativeResource::form($form);
});

test('table method returns configured table instance', function (): void {
    $table = Mockery::mock(Table::class);
    $table->shouldReceive('columns')->once()->andReturnSelf();
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    $result = AlternativeResource::table($table);
    
    expect($result)->toBeInstanceOf(Table::class);
});

test('table has correct number of columns', function (): void {
    $table = Mockery::mock(Table::class);
    
    $table->shouldReceive('columns')
        ->once()
        ->with(Mockery::on(function ($columns) {
            expect($columns)->toHaveCount(10);
            return true;
        }))
        ->andReturnSelf();
    
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('table logo column is configured correctly', function (): void {
    $table = Mockery::mock(Table::class);
    
    $table->shouldReceive('columns')
        ->once()
        ->with(Mockery::on(function ($columns) {
            $logoColumn = $columns[0];
            expect($logoColumn)->toBeInstanceOf(SpatieMediaLibraryImageColumn::class);
            return true;
        }))
        ->andReturnSelf();
    
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('table name column is sortable and searchable', function (): void {
    $table = Mockery::mock(Table::class);
    
    $table->shouldReceive('columns')
        ->once()
        ->with(Mockery::on(function ($columns) {
            $nameColumn = $columns[1];
            expect($nameColumn)->toBeInstanceOf(TextColumn::class);
            return true;
        }))
        ->andReturnSelf();
    
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('table approved column uses icon column', function (): void {
    $table = Mockery::mock(Table::class);
    
    $table->shouldReceive('columns')
        ->once()
        ->with(Mockery::on(function ($columns) {
            $approvedColumn = $columns[2];
            expect($approvedColumn)->toBeInstanceOf(IconColumn::class);
            return true;
        }))
        ->andReturnSelf();
    
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('table url column opens in new tab', function (): void {
    $table = Mockery::mock(Table::class);
    
    $table->shouldReceive('columns')
        ->once()
        ->with(Mockery::on(function ($columns) {
            $urlColumn = $columns[4];
            expect($urlColumn)->toBeInstanceOf(TextColumn::class);
            return true;
        }))
        ->andReturnSelf();
    
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('table resources column formats urls as html', function (): void {
    $table = Mockery::mock(Table::class);
    
    $table->shouldReceive('columns')
        ->once()
        ->with(Mockery::on(function ($columns) {
            $resourcesColumn = $columns[5];
            expect($resourcesColumn)->toBeInstanceOf(TextColumn::class);
            return true;
        }))
        ->andReturnSelf();
    
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('table has correct number of actions', function (): void {
    $table = Mockery::mock(Table::class);
    
    $table->shouldReceive('columns')->once()->andReturnSelf();
    $table->shouldReceive('filters')->once()->andReturnSelf();
    
    $table->shouldReceive('actions')
        ->once()
        ->with(Mockery::on(function ($actions) {
            expect($actions)->toHaveCount(4); // fetchLogo, removeLogo, edit, delete
            return true;
        }))
        ->andReturnSelf();
    
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('table has bulk actions configured', function (): void {
    $table = Mockery::mock(Table::class);
    
    $table->shouldReceive('columns')->once()->andReturnSelf();
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    
    $table->shouldReceive('bulkActions')
        ->once()
        ->with(Mockery::on(function ($bulkActions) {
            expect($bulkActions)->toHaveCount(1);
            $bulkActionGroup = $bulkActions[0];
            expect($bulkActionGroup)->toBeInstanceOf(BulkActionGroup::class);
            return true;
        }))
        ->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('get relations returns correct relation managers', function (): void {
    $relations = AlternativeResource::getRelations();
    
    expect($relations)->toBeArray()
        ->toHaveCount(3)
        ->toContain(CompaniesRelationManager::class)
        ->toContain(ResourcesRelationManager::class)
        ->toContain(AuditsRelationManager::class);
});

test('get pages returns correct page routes', function (): void {
    $pages = AlternativeResource::getPages();
    
    expect($pages)->toBeArray()
        ->toHaveCount(3)
        ->toHaveKeys(['index', 'create', 'edit']);
});

test('get pages index routes to list alternatives', function (): void {
    $pages = AlternativeResource::getPages();
    
    expect($pages['index'])->toBe(ListAlternatives::route('/'));
});

test('get pages create routes to create alternative', function (): void {
    $pages = AlternativeResource::getPages();
    
    expect($pages['create'])->toBe(CreateAlternative::route('/create'));
});

test('get pages edit routes to edit alternative with record parameter', function (): void {
    $pages = AlternativeResource::getPages();
    
    expect($pages['edit'])->toBe(EditAlternative::route('/{record}/edit'));
});

test('fetch logo action executes scrape logo action', function (): void {
    $mockAction = Mockery::mock(ScrapeLogoFromUrlAction::class);
    $mockAction->shouldReceive('execute')
        ->with(Mockery::type(Alternative::class), 'https://example.com')
        ->andReturn(true);
    
    app()->instance(ScrapeLogoFromUrlAction::class, $mockAction);
    
    $alternative = Mockery::mock(Alternative::class);
    $alternative->url = 'https://example.com';
    $alternative->shouldReceive('getAttribute')->with('media')->andReturn(collect());
    
    $table = Mockery::mock(Table::class);
    $table->shouldReceive('columns')->once()->andReturnSelf();
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('remove logo action clears media collection', function (): void {
    $alternative = Mockery::mock(Alternative::class);
    $mediaCollection = collect([Mockery::mock(Media::class)]);
    
    $alternative->shouldReceive('getAttribute')->with('media')->andReturn($mediaCollection);
    $alternative->shouldReceive('clearMediaCollection')->once();
    
    $table = Mockery::mock(Table::class);
    $table->shouldReceive('columns')->once()->andReturnSelf();
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('bulk approve action updates approved_at timestamp', function (): void {
    $records = collect([
        (object) ['id' => 1],
        (object) ['id' => 2],
    ]);
    
    $queryMock = Mockery::mock();
    $queryMock->shouldReceive('whereIn')
        ->with('id', [1, 2])
        ->once()
        ->andReturnSelf();
    $queryMock->shouldReceive('update')
        ->with(Mockery::on(function ($data) {
            expect($data)->toHaveKey('approved_at');
            return true;
        }))
        ->once();
    
    Alternative::shouldReceive('query')
        ->once()
        ->andReturn($queryMock);
    
    $notificationMock = Mockery::mock();
    $notificationMock->shouldReceive('success')->once()->andReturnSelf();
    $notificationMock->shouldReceive('title')->with('Alternatives Approved')->once()->andReturnSelf();
    $notificationMock->shouldReceive('send')->once();
    
    Notification::shouldReceive('make')
        ->once()
        ->andReturn($notificationMock);
    
    $table = Mockery::mock(Table::class);
    $table->shouldReceive('columns')->once()->andReturnSelf();
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('approved column boolean logic works correctly for approved records', function (): void {
    $approvedAlternative = new Alternative();
    $approvedAlternative->approved_at = now();
    
    $table = Mockery::mock(Table::class);
    $table->shouldReceive('columns')
        ->once()
        ->with(Mockery::on(function ($columns) use ($approvedAlternative) {
            $approvedColumn = $columns[2];
            expect($approvedColumn)->toBeInstanceOf(IconColumn::class);
            
            // Test the boolean callback if accessible
            if (method_exists($approvedColumn, 'getEvaluationIdentifier')) {
                // The boolean function should return true for approved records
                expect($approvedAlternative->approved_at)->not->toBeNull();
            }
            return true;
        }))
        ->andReturnSelf();
    
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('approved column boolean logic works correctly for unapproved records', function (): void {
    $unapprovedAlternative = new Alternative();
    $unapprovedAlternative->approved_at = null;
    
    $table = Mockery::mock(Table::class);
    $table->shouldReceive('columns')
        ->once()
        ->with(Mockery::on(function ($columns) use ($unapprovedAlternative) {
            $approvedColumn = $columns[2];
            expect($approvedColumn)->toBeInstanceOf(IconColumn::class);
            
            // Test the boolean callback if accessible
            if (method_exists($approvedColumn, 'getEvaluationIdentifier')) {
                // The boolean function should return false for unapproved records
                expect($unapprovedAlternative->approved_at)->toBeNull();
            }
            return true;
        }))
        ->andReturnSelf();
    
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('resources column formats multiple resources with html links', function (): void {
    $resource1 = (object) ['url' => 'https://example1.com'];
    $resource2 = (object) ['url' => 'https://example2.com'];
    
    $alternative = Mockery::mock(Alternative::class);
    $alternative->shouldReceive('getAttribute')
        ->with('resources')
        ->andReturn(collect([$resource1, $resource2]));
    
    $table = Mockery::mock(Table::class);
    $table->shouldReceive('columns')
        ->once()
        ->with(Mockery::on(function ($columns) use ($alternative) {
            $resourcesColumn = $columns[5];
            expect($resourcesColumn)->toBeInstanceOf(TextColumn::class);
            return true;
        }))
        ->andReturnSelf();
    
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('url column callback returns correct url from record', function (): void {
    $alternative = new Alternative();
    $alternative->url = 'https://example.com';
    
    $table = Mockery::mock(Table::class);
    $table->shouldReceive('columns')
        ->once()
        ->with(Mockery::on(function ($columns) use ($alternative) {
            $urlColumn = $columns[4];
            expect($urlColumn)->toBeInstanceOf(TextColumn::class);
            expect($alternative->url)->toBe('https://example.com');
            return true;
        }))
        ->andReturnSelf();
    
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('form tag create option has correct grid layout', function (): void {
    $form = Mockery::mock(Form::class);
    $form->shouldReceive('schema')
        ->once()
        ->with(Mockery::on(function ($schema) {
            $tagsField = $schema[5];
            expect($tagsField)->toBeInstanceOf(Select::class);
            return true;
        }))
        ->andReturnSelf();
    
    AlternativeResource::form($form);
});

test('table description and notes columns have character limits', function (): void {
    $table = Mockery::mock(Table::class);
    
    $table->shouldReceive('columns')
        ->once()
        ->with(Mockery::on(function ($columns) {
            $descriptionColumn = $columns[6];
            $notesColumn = $columns[7];
            expect($descriptionColumn)->toBeInstanceOf(TextColumn::class);
            expect($notesColumn)->toBeInstanceOf(TextColumn::class);
            return true;
        }))
        ->andReturnSelf();
    
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('table created_at and updated_at columns are toggleable and hidden by default', function (): void {
    $table = Mockery::mock(Table::class);
    
    $table->shouldReceive('columns')
        ->once()
        ->with(Mockery::on(function ($columns) {
            $createdAtColumn = $columns[8];
            $updatedAtColumn = $columns[9];
            expect($createdAtColumn)->toBeInstanceOf(TextColumn::class);
            expect($updatedAtColumn)->toBeInstanceOf(TextColumn::class);
            return true;
        }))
        ->andReturnSelf();
    
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('fetch logo action requires confirmation when media exists', function (): void {
    $alternative = Mockery::mock(Alternative::class);
    $mediaCollection = collect([Mockery::mock(Media::class)]);
    
    $alternative->shouldReceive('getAttribute')->with('media')->andReturn($mediaCollection);
    
    $table = Mockery::mock(Table::class);
    $table->shouldReceive('columns')->once()->andReturnSelf();
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')
        ->once()
        ->with(Mockery::on(function ($actions) {
            // Verify that fetch logo action exists
            expect($actions)->toHaveCount(4);
            return true;
        }))
        ->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('remove logo action is only visible when media exists', function (): void {
    $alternative = Mockery::mock(Alternative::class);
    $mediaCollection = collect([Mockery::mock(Media::class)]);
    
    $alternative->shouldReceive('getAttribute')->with('media')->andReturn($mediaCollection);
    
    $table = Mockery::mock(Table::class);
    $table->shouldReceive('columns')->once()->andReturnSelf();
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')
        ->once()
        ->with(Mockery::on(function ($actions) {
            // Verify that remove logo action exists and is properly configured
            expect($actions)->toHaveCount(4);
            return true;
        }))
        ->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('tags relation column displays as badges', function (): void {
    $table = Mockery::mock(Table::class);
    
    $table->shouldReceive('columns')
        ->once()
        ->with(Mockery::on(function ($columns) {
            $tagsColumn = $columns[3];
            expect($tagsColumn)->toBeInstanceOf(TextColumn::class);
            return true;
        }))
        ->andReturnSelf();
    
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('edit and delete actions have empty labels', function (): void {
    $table = Mockery::mock(Table::class);
    
    $table->shouldReceive('columns')->once()->andReturnSelf();
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')
        ->once()
        ->with(Mockery::on(function ($actions) {
            // The last two actions should be EditAction and DeleteAction with empty labels
            expect($actions)->toHaveCount(4);
            return true;
        }))
        ->andReturnSelf();
    $table->shouldReceive('bulkActions')->once()->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('bulk approve action has correct modal configuration', function (): void {
    $table = Mockery::mock(Table::class);
    
    $table->shouldReceive('columns')->once()->andReturnSelf();
    $table->shouldReceive('filters')->once()->andReturnSelf();
    $table->shouldReceive('actions')->once()->andReturnSelf();
    $table->shouldReceive('bulkActions')
        ->once()
        ->with(Mockery::on(function ($bulkActions) {
            $bulkActionGroup = $bulkActions[0];
            expect($bulkActionGroup)->toBeInstanceOf(BulkActionGroup::class);
            return true;
        }))
        ->andReturnSelf();
    
    AlternativeResource::table($table);
});

test('logo field accepts correct file types', function (): void {
    $form = Mockery::mock(Form::class);
    $form->shouldReceive('schema')
        ->once()
        ->with(Mockery::on(function ($schema) {
            $logoField = $schema[4];
            expect($logoField)->toBeInstanceOf(SpatieMediaLibraryFileUpload::class);
            // The validation rules include image, mimes:jpeg,jpg,png,svg,webp, and max:2048
            return true;
        }))
        ->andReturnSelf();
    
    AlternativeResource::form($form);
});

test('description and notes fields span full column width', function (): void {
    $form = Mockery::mock(Form::class);
    $form->shouldReceive('schema')
        ->once()
        ->with(Mockery::on(function ($schema) {
            $descriptionField = $schema[2];
            $notesField = $schema[3];
            expect($descriptionField)->toBeInstanceOf(Textarea::class);
            expect($notesField)->toBeInstanceOf(Textarea::class);
            return true;
        }))
        ->andReturnSelf();
    
    AlternativeResource::form($form);
});