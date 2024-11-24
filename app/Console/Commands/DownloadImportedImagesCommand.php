<?php

namespace App\Console\Commands;

use App\Models\Person;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use WatheqAlshowaiter\BackupTables\BackupTables;

class DownloadImportedImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:download-people-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        //BackupTables::generateBackup(Person::class);

        $peopleImagePath = storage_path('app/public/images/people/');
        $peopleOptimizedImagePath = storage_path('app/public/images/people/optimized/');

        File::makeDirectory(
            $peopleOptimizedImagePath,
            0755,
            true,
            true
        );

        $progressBar = $this->output->createProgressBar(Person::nonEmptyAvatar()->count());

        Person::nonEmptyAvatar()->chunk(70, function ($persons) use ($progressBar) {
            $persons->each(function ($person) use ($progressBar) {
                /**
                 * if file exists in the folders, don't download it again
                 */
                if (File::exists(
                    Storage::path(
                        'images/people/' . basename(parse_url($person->avatar, PHP_URL_PATH))
                    )
                )) {
                    $this->info("\nSkipped " . $person->name . "\n");

                    return;
                }

                if (! Str::isUrl($person->avatar)) {
                    $this->info("\nSkipped " . $person->name . " because non valid url\n");
                    $person->update(['avatar' => null]);

                    return;
                }

                $person
                    ->addMediaFromUrl(Str::isUrl($person->avatar) ?? null)
                    ->toMediaCollection();

                // after we download the image and move it from avatar column to media library
                // then we don't need it anymore. This will give us a flag for moved images
                $person->update(['avatar' => null]);

                $this->info("\ndownloaded logo for " . $person->name . "\n");

                $progressBar->advance();
            });
        });

        $progressBar->finish();

    }
}
