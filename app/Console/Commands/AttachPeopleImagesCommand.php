<?php

namespace App\Console\Commands;

use App\Models\Person;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttachPeopleImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:attach-images-people';

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
        // BackupTables::generateBackup(Person::class);

        $people = Person::query()->whereNotNull('notes');

        // $people(30, function ($people) use (&$succeeded, &$failed, $progressBar): void {
        $people->lazy()->each(callback: function (Person $person) use (&$succeeded, &$failed): void {

            /** @var Collection $notes * */
            $notes = $person->notes;

            if (isset($notes['notes']) && $notes['notes'] == 'image attached') {
                return;
            }

            if (! isset($notes['url'])) {
                return;
            }

            $imagePath = get_image_archive_path($notes['url'], 'people');
            DB::beginTransaction();
            try {
                if (! add_image_for_model($imagePath, $person)) {
                    $this->info("Failed to add image from folder for person:$person->name");

                    if (Str::isUrl($notes['url'])) {
                        $this->info('Try to Download it from Url..');

                        try {
                            $person->addMediaFromUrl($notes['url'])->toMediaCollection();
                            $this->info("Successfully add image from Url for person:$person->name");
                            $notes['notes'] = 'image attached';
                            $person->update(['notes' => $notes]);
                            $succeeded++;
                        } catch (Exception $e) {
                            $this->info("Failed to add image from url for person:$person->name");
                            $this->error($e->getMessage());
                            $failed++;
                        }
                    }
                } else {
                    $this->info("Successfully add image from folder for person:$person->name");
                    $succeeded++;
                }
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
