<?php

use App\Console\Commands\PerformDatabaseBackupCommand;
use Illuminate\Support\Facades\Schedule;

/**
 * Perform Database Backup & log info in the same daily log file
 */
Schedule::command(PerformDatabaseBackupCommand::class)
    ->everySixHours()
    ->appendOutputTo(
        storage_path('logs/laravel-' . date('Y-m-d') . '.log')
    );
