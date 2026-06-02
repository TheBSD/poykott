<?php

namespace App\Console\Commands;

use App\Models\Company;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportMatrixAlternativesImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:import-matrix-alternatives-images';

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
        $json = file_get_contents(database_path('seeders/data/14-israeli-tech-alternatives-logos.json'));
        $allData = json_decode($json, true);
        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($allData as $data) {
            $companyName = data_get($data, 'name');
            $companyUrl = data_get($data, 'website');
            $companyImageUrl = data_get($data, 'logo');
            $company = Company::query()->where('name', $companyName)->firstOr(function () use ($companyName, $companyUrl) {
                return Company::query()->create([
                    'name' => $companyName,
                    'url' => $companyUrl,
                    'approved_at' => now(),
                ]);
            });

            $notes = $company->notes;

            if (isset($notes['notes']) && $notes['notes'] == 'image attached') {
                $this->line("Skipped {$company->name}");

                continue;
            }

            $this->line("Importing logo for: {$company->name}\n");
            if (Str::isUrl($companyImageUrl)) {
                $this->info('Try to Download it from Url..');
                try {
                    // UsingResponse headers to fix 403 forbidden error when accessing images
                    $temp = tempnam(sys_get_temp_dir(), 'media');
                    Http::withHeaders([
                        'User-Agent' => 'Mozilla/5.0',
                    ])->sink($temp)->get($companyImageUrl);
                    $company->addMedia($temp)->toMediaCollection();
                    // $company->addMediaFromUrl($companyImageUrl)->toMediaCollection();
                    $this->info("Successfully add image from Url for company:$company->name");
                    $notes['notes'] = 'image attached';
                    $company->update(['notes' => $notes]);
                    $this->line("Imported: {$company->name}\n");
                } catch (Exception $e) {
                    $this->info("Failed to add image from url for company:$company->name");
                    $this->error($e->getMessage());
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info("\nProcessed Completed!");
    }
}
