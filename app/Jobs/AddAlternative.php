<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AddAlternative implements ShouldQueue
{
    use Queueable;

    public $alternative;

    /**
     * Create a new job instance.
     */
    public function __construct($alternative)
    {
        $this->alternative = $alternative;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
