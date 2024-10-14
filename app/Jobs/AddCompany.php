<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddCompany implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $company;

    /**
     * Create a new job instance.
     */
    public function __construct($company)
    {
        $this->company = $company;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
