<?php

use App\Console\Commands\PerformDatabaseBackupCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(PerformDatabaseBackupCommand::class)->everySixHours();
