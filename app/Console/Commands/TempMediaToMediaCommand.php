<?php

namespace App\Console\Commands;

use App\Jobs\ProcessTempMedia;
use App\Models\TempMedia;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

class TempMediaToMediaCommand extends Command
{
    protected $signature = 'temp-media-to-media';

    protected $description = 'Download temp media to media';

    /**
     * Execute the console command.
     *
     * @throws ConnectionException
     * @throws Throwable
     */
    public function handle(): void
    {
        $tempMedias = TempMedia::query()
            ->where('is_processed', false)
            ->get();

        $this->info("We have {$tempMedias->count()} to add");

        foreach ($tempMedias as $tempMedia) {
            ProcessTempMedia::dispatch($tempMedia);
        }
    }
}
