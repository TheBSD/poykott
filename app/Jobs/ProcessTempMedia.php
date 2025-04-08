<?php

namespace App\Jobs;

use App\Models\TempMedia;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessTempMedia implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected TempMedia $tempMedia
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @throws Throwable
     */
    public function handle(): void
    {
        $tempMedia = $this->tempMedia;

        DB::transaction(function () use ($tempMedia): void {
            $mediable = $tempMedia->mediable()->first();

            if (! $mediable) {
                $tempMedia->update(['is_processed' => true]);

                return;
            }

            if ($mediable->media()->exists()) {
                Log::warning("model ID {$mediable->id} has media already. skipping");
                $tempMedia->update(['is_processed' => true]);

                return;
            }

            $fixedUrl = encode_filename_in_url($tempMedia->url);
            $mediable->addMediaFromUrl($fixedUrl)->toMediaCollection();
            $tempMedia->update(['is_processed' => true]);
            Log::info("Successfully processed TempMedia ID {$tempMedia->id}");
        });

    }
}
