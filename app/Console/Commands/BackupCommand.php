<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'do:database-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a backup of the database and move old backups to oldBackups directory if you want and check for old backups if you want to delete it.';

    /**
     * Execute the console command.
     */
    public function handle(): ?bool
    {
        if (! File::isDirectory(database_path('backups'))) {
            File::makeDirectory(database_path('backups'));
            $this->info('Directory created: ' . database_path('backups'));
        }

        $fileExists = File::glob(database_path('*.sqlite'));
        $fileExistsInDirectory = File::glob(database_path('*' . DIRECTORY_SEPARATOR . '*.sqlite'));
        $fileExistsTest = Arr::flatten([$fileExists, $fileExistsInDirectory]);
        $alreadyBackups = File::glob(database_path('backups' . DIRECTORY_SEPARATOR . '*.sql'));
        $oldBackupsFiles = File::glob(database_path('oldBackups' . DIRECTORY_SEPARATOR . '*.sql'));

        if (collect($fileExistsTest)->isNotEmpty()) {
            foreach ($fileExistsTest as $file) {
                $fileNameInDirectory = 'backup-' . date('Y-m-d-H-i-s') . '-' . Str::random(2) . '.sql';
                File::copy($file, database_path('backups' . DIRECTORY_SEPARATOR . $fileNameInDirectory));
                $this->info('Backup created successfullt: ' . database_path('backups' . DIRECTORY_SEPARATOR . $fileNameInDirectory));
            }
        } else {
            $this->error('Backup failed files not found in : ' . database_path());

            return false;
        }

        if (collect($alreadyBackups)->isNotEmpty()) {
            if ($this->confirm('Do you want to move already backups files to oldBackups directory (if no it is mean old backups are deleted)?', true)) {
                if (! File::exists(database_path('oldBackups'))) {
                    File::makeDirectory(database_path('oldBackups'));
                    $this->info('Old Backups created successfully: ' . database_path('oldBackups'));
                }
                foreach ($alreadyBackups as $oldBackup) {
                    File::move($oldBackup, database_path('oldBackups' . DIRECTORY_SEPARATOR . basename((string) $oldBackup)));
                    $this->info('Old Backups moved successfully: ' . database_path('oldBackups' . DIRECTORY_SEPARATOR . basename((string) $oldBackup)));
                }
            } else {
                foreach ($alreadyBackups as $oldBackup) {
                    File::delete($oldBackup);
                    $this->info('Old Backups deleted successfully: ' . $oldBackup);
                }
            }
        }

        $oldBackupsFilesCount = count($oldBackupsFiles);
        if ($this->confirm('You have old backups file count : ' . $oldBackupsFilesCount . ' file do you want to delete them ?')) {
            foreach ($oldBackupsFiles as $oldBackupFile) {
                File::delete($oldBackupFile);
                $this->info('Old Backups deleted successfully: ' . $oldBackupFile);
            }
        } else {
            $this->info('Old Backups not deleted');
        }

        $this->info('-----------------------------------------');
        $this->info('|Backup process completed successfully !|');
        $this->info('-----------------------------------------');

        return null;
    }
}
