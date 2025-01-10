<?php

namespace App\Console\Commands;

use App\Enums\ResourceType;
use App\Models\Company;
use App\Models\OfficeLocation;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UnicornTrackerCommand extends Command
{
    protected $signature = 'import:unicorn-tracker';

    public function handle(): void
    {
        $json = file_get_contents(database_path('seeders/data/8-israel-unicorn-tracker.json'));

        $allData = json_decode($json, true);

        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($allData as $data) {

            /**
             * title => Company->name
             */
            $lowerCompanyName = Str::of(data_get($data, 'title'))->lower()->trim()->value();
            $company = Company::query()->whereRaw('Lower(name) = ?', [$lowerCompanyName])->first();

            $dataFields = [
                'url' => data_get($data, 'website'),
                'approved_at' => now(),
            ];

            if (is_null($company)) {
                $company = Company::query()->create(array_merge([
                    'name' => trim((string) data_get($data, 'title')),
                ], $dataFields));
            }

            // update url only if no previous url
            if (! $company->wasRecentlyCreated) {
                $company->update($dataFields);

                if (empty($company->url)) {
                    $company->update($dataFields);
                }
            }

            /**
             * Company->resources
             */
            $company->resources()->updateOrCreate([
                'url' => 'https://www.usisrael.co/unicorn-tracker',
            ], [
                'type' => ResourceType::UsIsraelUnicornTracker,
            ]);

            /**
             * solution => Company->tags
             */
            $solution = data_get($data, 'solution');
            $lowerTagName = Str::of($solution)->lower()->trim()->value();
            $tag = Tag::query()->whereRaw('LOWER(name) = ?', [$lowerTagName])->first();

            if (is_null($tag)) {
                $tag = Tag::query()->create([
                    'name' => Str::of($solution)->squish()->value(),
                ]);
            }

            if ($company->doesntHaveTag($tag)) {
                $company->tagsRelation()->attach($tag);
            }

            /**
             * state => Company->OfficeLocation
             */
            $state = data_get($data, 'state');
            $stateLowerName = Str::of($state)->lower()->trim()->value();
            $officeLocation = OfficeLocation::query()
                ->whereRaw('LOWER(name) = ?', strtolower($stateLowerName))
                ->first();

            if (is_null($officeLocation)) {
                $officeLocation = OfficeLocation::query()->create([
                    'name' => Str::of($state)->squish()->value(),
                ]);
            }

            if ($company->doesntHaveOfficeLocation($officeLocation)) {
                $company->officeLocations()->attach($officeLocation);
            }

            $this->line("Processed importing: {$company->name}");
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info("\nProcessed Completed!");
    }
}
