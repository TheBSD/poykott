<?php

use App\Console\Commands\GenerateSitemap;
use App\Console\Commands\PerformDatabaseBackupCommand;
use App\Console\Commands\TempMediaToMediaCommand;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Support\Facades\Schedule;

/**
 * Perform Database Backup & log info in the same daily log file
 */
Schedule::command(PerformDatabaseBackupCommand::class)
    ->everySixHours()
    ->appendOutputTo(
        storage_path('logs/laravel-' . date('Y-m-d') . '.log')
    );

Schedule::command(PruneCommand::class)->daily();
Schedule::command(TempMediaToMediaCommand::class)->daily();

Schedule::command(GenerateSitemap::class)->daily();
