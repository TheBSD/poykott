<?php

namespace App\Console\Commands;

use App\Models\Company;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttachCompaniesImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attach-images-companies';

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
            $count = Company::query()->whereNotNull('notes')->count()
        );

        $succeeded = 0;
        $failed = 0;

        $companies = Company::query()->whereNotNull('notes');

        //$companies->chunk(30, function ($companies) use ($progressBar): void {
        $companies->lazy()->each(callback: function (Company $company) use (&$succeeded, &$failed): void {

            /** @var Collection $notes * */
            $notes = $company->notes;

            if ($notes->first() == 'image attached') {
                return;
            }

            $url = $notes->groupBy('url')->keys()->first();

            $imagePath = get_image_archive_path($url, 'companies');

            DB::beginTransaction();

            try {
                if (! add_image_for_model($imagePath, $company)) {
                    $this->info("Failed to add image from folder for company:$company->name");

                    if (Str::isUrl($url)) {
                        $this->info('Try to Download it from Url..');

                        try {
                            $company->addMediaFromUrl($url)->toMediaCollection();
                            $this->info("Successfully add image from Url for person:$company->name");
                            $succeeded++;
                        } catch (Exception) {
                            $this->info("Failed to add image from url for person:$company->name");
                            $failed++;
                        }
                    }
                } else {
                    $this->info("Successfully add image from folder for company:$company->name");
                    $succeeded++;
                }

                $company->update(['notes' => 'image attached']);

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                $this->error($e->getMessage());
            }
        });
        //});

        $progressBar->finish();

        $successRate = $succeeded / $count * 100;
        $failedRate = $failed / $count * 100;
        $this->info("Success rate: $successRate\nFailed rate: $failedRate");
    }
}
