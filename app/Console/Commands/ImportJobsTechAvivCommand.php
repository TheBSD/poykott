<?php

namespace App\Console\Commands;

use App\Enums\ResourceType;
use App\Models\Company;
use App\Models\OfficeLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportJobsTechAvivCommand extends Command
{
    protected $signature = 'import:jobs-tech-aviv';

    protected $description = 'Command description';

    public function handle(): void
    {
        $files = glob(database_path('seeders/data/6-jobs*.json'));

        foreach ($files as $file) {

            $allData = json_decode(file_get_contents($file), true);

            $progressBar = $this->output->createProgressBar(count($allData));

            $jobs = data_get($allData, 'jobs');
            foreach ($jobs as $job) {
                $companyLowerName = Str::of(data_get($job, 'companyName'))
                    ->lower()
                    ->trim()
                    ->value();

                $company = Company::query()->whereRaw('LOWER(name) = ?', [$companyLowerName])->first();

                if (is_null($company)) {
                    $company = Company::query()->create([
                        'name' => data_get($job, 'companyName'),
                        'url' => data_get($job, 'companyDomain'),
                    ]);
                }

                $resourceUrl = 'https://jobs.techaviv.com/jobs';

                $companyResource = $company->resources()->updateOrCreate([
                    'url' => $resourceUrl,
                ], [
                    'type' => ResourceType::TechAviv,
                ]);

                $locations = data_get($job, 'locations');

                foreach ($locations as $location) {
                    $locationLowerName = Str::of($location)
                        ->lower()
                        ->squish()
                        ->value();

                    if (Str::of($locationLowerName)->lower()->contains([
                        'remote',
                        'https',
                        ', , AU',
                        '*BFF + Jobs*',
                        'England)',
                    ])) {
                        continue;
                    }

                    $officeLocation = OfficeLocation::query()->whereRaw('LOWER(name) = ?', strtolower($locationLowerName))->first();

                    if (is_null($officeLocation)) {
                        $officeLocation = OfficeLocation::query()->create([
                            'name' => Str::of($location)->squish()->value(),
                        ]);
                    }

                    if ($company->officeLocations()->where('office_location_id', $officeLocation->id)->doesntExist()) {
                        $company->officeLocations()->attach($officeLocation);
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
