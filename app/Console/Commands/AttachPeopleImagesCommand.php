<?php

namespace App\Console\Commands;

use App\Models\Person;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function App\Helpers\add_image_for_model;
use function App\Helpers\get_image_archive_path;

class AttachPeopleImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attach-images-people';

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
        $progressBar = $this->output->createProgressBar(
            $count = Person::query()->whereNotNull('notes')->count()
        );

        $succeeded = 0;
        $failed = 0;

        // Backup for testing and tracing
        //BackupTables::generateBackup(Person::class);

        $people = Person::query()->whereNotNull('notes');

        //$people(30, function ($people) use (&$succeeded, &$failed, $progressBar): void {
        $people->lazy()->each(callback: function (Person $person) use (&$succeeded, &$failed): void {

            /** @var Collection $notes * */
            $notes = $person->notes;

            if ($notes->first() == 'image attached') {
                return;
            }

            $url = $notes->groupBy('url')->keys()->first();

            $imagePath = get_image_archive_path($url, 'people');

            DB::beginTransaction();

            try {
                if (! add_image_for_model($imagePath, $person)) {
                    $this->info("Failed to add image from folder for person:$person->name");

                    if (Str::isUrl($url)) {
                        $this->info('Try to Download it from Url..');

                        try {
                            $person->addMediaFromUrl($url)->toMediaCollection();
                            $this->info("Successfully add image from Url for person:$person->name");
                        } catch (Exception) {
                            $this->info("Failed to add image from url for person:$person->name");
                            $failed++;
                        }
                    }
                } else {
                    $this->info("Successfully add image from folder for person:$person->name");
                    $succeeded++;
                }

                $person->update(['notes' => 'image attached']);

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                $this->error($e->getMessage());
            }
        });

        $progressBar->finish();

        $successRate = $succeeded / $count * 100;
        $failedRate = $failed / $count * 100;
        $this->info("Success rate: $successRate\nFailed rate: $failedRate");
    }
}
