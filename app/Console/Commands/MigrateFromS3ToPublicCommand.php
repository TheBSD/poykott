<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MigrateFromS3ToPublicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:media-from-s3-to-public';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Media::all()->each(function (Media $media): void {
            $media->update([
                'disk' => 'public',
                'conversions_disk' => 'public',
            ]);
        });
    }
}
