<?php

namespace App\Console\Commands;

use App\Enums\ResourceType;
use App\Models\Company;
use App\Models\Investor;
use App\Models\OfficeLocation;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function App\Helpers\add_image_urls_to_notes;

class ImportJobCompaniesTechAvivCommand extends Command
{
    protected $signature = 'import:job-companies-tech-aviv';

    protected $description = 'Command description';

    public function handle(): void
    {
        $json = file_get_contents(database_path('seeders/data/5-job-companies.json'));

        $allData = json_decode($json, true);
        $companies = data_get($allData, 'companies');

        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($companies as $data) {

            $lowerName = Str::of(data_get($data, 'name'))->lower()->trim()->value();
            $dataFields = [
                'url' => data_get($data, 'domain') ?? data_get($data, 'website.url'),
                'description' => data_get($data, 'description'),
                'approved_at' => now(),
            ];
            $company = Company::query()->whereRaw('LOWER(name) = ?', [$lowerName])->first();

            if (is_null($company)) { // create if not exists
                $company = Company::query()->create(array_merge($dataFields, [
                    'name' => data_get($data, 'name'),
                ]));

                //if (data_get($data, 'logos.manual.src')) {
                //    $company->logo()->create([
                //        'path' => data_get($data, 'logos.manual.src'),
                //    ]);
                //}

                //add_image_urls_to_notes(data_get($data, 'logos.manual.src'), $company, $this);
            }

            if (empty($company->description)) {
                $company->update(['description' => data_get($data, 'description')]);
            }

            if (empty($company->url)) {
                $company->update([
                    'url' => data_get($data, 'domain') ?? data_get($data, 'website.url'),
                ]);
            }

            //if (empty($company->logo)) {
            //    $company->logo()->update([
            //        'path' => data_get($data, 'logos.manual.src'),
            //    ]);
            //}

            add_image_urls_to_notes(data_get($data, 'logos.manual.src'), $company, $this);

            $company->update(['employee_count' => data_get($data, 'staffCount')]);

            $resourceUrl = 'https://jobs.techaviv.com/companies';

            $companyResource = $company->resources()->updateOrCreate([
                'url' => $resourceUrl,
            ], [
                'type' => ResourceType::TechAviv,
            ]);

            $investors = data_get($data, 'investors');

            foreach ($investors as $investorName) {
                $lowerName = Str::of($investorName)->lower()->trim()->value();
                $investor = Investor::query()->whereRaw('LOWER(name) = ?', [$lowerName])->first();

                if (is_null($investor)) { // create if not exists
                    $investor = Investor::query()->create([
                        'name' => $investorName,
                        'approved_at' => now(),
                    ]);
                }

                $investorResource = $investor->resources()->updateOrCreate([
                    'url' => $resourceUrl,
                ], [
                    'type' => ResourceType::TechAviv,
                ]);

                if ($company->investors()->where('investor_id', $investor->id)->doesntExist()) {
                    $company->investors()->attach($investor);
                }
            }

            $officeLocations = data_get($data, 'officeLocations');

            foreach ($officeLocations as $officeLocationData) {
                $officeLocationLowerName = Str::of($officeLocationData)->lower()->squish()->value();
                $officeLocation = OfficeLocation::query()->whereRaw('LOWER(name) = ?', [$officeLocationLowerName])->first();
                if (is_null($officeLocation)) {
                    $officeLocation = OfficeLocation::query()->create([
                        'name' => Str::of($officeLocationData)->squish()->value(),
                    ]);
                }

                if ($company->officeLocations()->where('office_location_id', $officeLocation->id)->doesntExist()) {
                    $company->officeLocations()->attach($officeLocation);
                }
            }

            $markets = data_get($data, 'markets');

            foreach ($markets as $market) {
                $lowerTagName = Str::of($market)->lower()->trim()->value();
                $tag = Tag::query()->whereRaw('LOWER(name) = ?', [$lowerTagName])->first();

                if (is_null($tag)) {
                    $tag = Tag::query()->create([
                        'name' => trim((string) $market),
                    ]);
                }

                if (! $tag->wasRecentlyCreated) {
                    $tag->update(['name' => trim((string) $market)]);
                }

                if ($company->tagsRelation()->where('tag_id', $tag->id)->doesntExist()) {
                    $company->tagsRelation()->attach($tag->id, ['taggable_id' => $company->id]);
                }
            }

            $this->line("Processed importing: {$company->name}");
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info("\nProcessed Completed!");
    }
}
