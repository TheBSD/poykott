<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportJobsTechAvivCommand extends Command
{
    protected $signature = 'import:jobs-tech-aviv';

    protected $description = 'Command description';

    public function handle(): void
    {
        $files = glob(storage_path('app/private/6-jobs*.json'));

        foreach ($files as $file) {

            $allData = json_decode(file_get_contents($file), true);

            $progressBar = $this->output->createProgressBar(count($allData));

            $jobs = data_get($allData, 'jobs');
            foreach ($jobs as $job) {
                $companyLowerName = Str::of(data_get($job, 'companyName'))
                    ->lower()
                    ->trim()
                    ->value();

                $company = Company::whereRaw('LOWER(name) = ?', [$companyLowerName])->first();

                if (is_null($company)) {
                    $company = Company::create([
                        'name' => data_get($job, 'companyName'),
                        'url' => data_get($job, 'companyDomain'),
                    ]);
                }

                $locations = data_get($job, 'locations');

                foreach ($locations as $location) {
                    $locationLowerName = Str::of($location)
                        ->lower()
                        ->replace(',', '')
                        ->trim()
                        ->value();

                    $officeLocation = $company->officeLocations()
                        ->whereRaw('LOWER(name) = ?', [$locationLowerName])
                        ->first();

                    dump($location, $locationLowerName);

                    if (is_null($officeLocation)) {
                        $officeLocation = $company->officeLocations()->create([
                            'name' => Str::of($location)->replace(',', '')->trim(),
                        ]);
                    }
                }

                $this->line("Processed importing: {$company->name}");
                $progressBar->advance();
            }

            $progressBar->finish();

            $this->info("\nProcessed Completed!");
        }
    }
}
