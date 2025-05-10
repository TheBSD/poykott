<?php

namespace App\Console\Commands;

use App\Jobs\TestJob;
use Illuminate\Console\Command;

class TestJobCommand extends Command
{
    protected $signature = 'test-job';

    protected $description = 'Know if the queue is working, especially in production';

    public function handle(): void
    {
        TestJob::dispatch();
    }
}
