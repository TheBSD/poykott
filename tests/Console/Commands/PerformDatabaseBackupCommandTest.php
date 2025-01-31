<?php

use App\Console\Commands\PerformDatabaseBackupCommand;
use Illuminate\Support\Facades\File;

test('perform database backup', function (): void {
    File::shouldReceive('copy')
        ->once()
        ->with(database_path('database.sqlite'), Mockery::type('string'));

    File::shouldReceive('makeDirectory')
        ->once()
        ->with(database_path('backups'), $mode = octdec(755), $recursive = false, $force = true);

    File::shouldReceive('glob')
        ->once()
        ->with(database_path('backups/*.sql'))
        ->andReturn([
            database_path('backups/backup-1.sql'),
            database_path('backups/backup-2.sql'),
            database_path('backups/backup-3.sql'),
            database_path('backups/backup-4.sql'),
            database_path('backups/backup-5.sql'),
        ]);

    File::shouldReceive('delete')
        ->times(1)
        ->with(database_path('backups/backup-1.sql'))
        ->andReturnTrue();

    $this->artisan(PerformDatabaseBackupCommand::class)
        ->assertExitCode(0);
});
