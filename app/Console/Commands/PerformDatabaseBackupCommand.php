<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PerformDatabaseBackupCommand extends Command
{
    /**
     * Number of backup files we keep until we delete the old ones.
     */
    const NUMBER_OF_BACKUP_FILES = 4;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perform:database-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform a database backup.';

    /**
     * Execute the console command.
     */
    public function handle(): ?bool
    {
        $filePath = database_path('backups/backup-' . now()->timestamp . '.sql');
        $backupDirectory = database_path('backups');

        $this->createDirectoryIfNotExists($backupDirectory);

        File::copy(database_path('database.sqlite'), $filePath);
        $this->info('Backup created: ' . $filePath);

        $glob = File::glob(database_path('backups/*.sql'));

        collect($glob)
            ->sort()
            ->reverse()
            ->slice(self::NUMBER_OF_BACKUP_FILES)
            ->filter(
                fn (mixed $backup): bool => is_string($backup),
            )
            ->each(
                fn (string $backup): bool => File::delete($backup),
            );

        return CommandAlias::SUCCESS;
    }

    private function createDirectoryIfNotExists(string $directory): void
    {
        if (File::exists($directory)) {
            return;
        }

        File::makeDirectory($directory, $mode = 0755, $recursive = false, $force = true);
        $this->info('Directory created: ' . database_path($directory));
    }
}
