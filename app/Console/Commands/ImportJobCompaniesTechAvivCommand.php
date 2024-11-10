<?php

namespace App\Console\Commands;

use App\Enums\ResourceType;
use App\Models\Company;
use App\Models\Investor;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportJobCompaniesTechAvivCommand extends Command
{
    protected $signature = 'import:job-companies-tech-aviv';

    protected $description = 'Command description';

    public function handle(): void
    {
        $json = file_get_contents(storage_path('app/private/5-job-companies.json'));

        $allData = json_decode($json, true);
        $companies = data_get($allData, 'companies');

        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($companies as $data) {

            $lowerName = Str::of(data_get($data, 'name'))->lower()->trim()->value();
            $dataFields = [
                'url' => data_get($data, 'domain') ?? data_get($data, 'website.url'),
                'description' => data_get($data, 'description'),
                'logo' => data_get($data, 'logos.manual.src'),
            ];
            $company = Company::whereRaw('LOWER(name) = ?', [$lowerName])->first();

            if (is_null($company)) { // create if not exists
                $company = Company::create(array_merge($dataFields, [
                    'name' => data_get($data, 'name'),
                ]));
            }

            if (empty($company->description)) {
                $company->update(['description' => data_get($data, 'description')]);
            }

            if (empty($company->url)) {
                $company->update([
                    'url' => data_get($data, 'domain') ?? data_get($data, 'website.url'),
                ]);
            }

            if (empty($company->logo)) {
                $company->update([
                    'logo' => data_get($data, 'logos.manual.src'),
                ]);
            }

            $company->update(['employee_count' => data_get($data, 'staffCount')]);

            $resourceUrl = 'https://jobs.techaviv.com/jobs';

            $company->companyResources()->updateOrCreate([
                'url' => $resourceUrl,
            ], [
                'title' => ResourceType::TechAviv,
            ]);

            $resource = $company->resources()->updateOrCreate([
                'url' => $resourceUrl,
            ], [
                'type' => ResourceType::TechAviv,
            ]);

            $investors = data_get($data, 'investors');

            foreach ($investors as $investorName) {
                $lowerName = Str::of($investorName)->lower()->trim()->value();
                $investor = Investor::whereRaw('LOWER(name) = ?', [$lowerName])->first();

                if (is_null($investor)) { // create if not exists
                    $investor = Investor::create([
                        'name' => $investorName,
                    ]);
                }

                if ($company->investors()->where('investor_id', $investor->id)->doesntExist()) {
                    $company->investors()->attach($investor);
                }
            }

            $officeLocations = data_get($data, 'officeLocations');

            foreach ($officeLocations as $officeLocation) {

                $company->officeLocations()->updateOrCreate([
                    'name' => $officeLocation,
                ]);
            }

            $markets = data_get($data, 'markets');

            foreach ($markets as $market) {
                $lowerTagName = Str::of($market)->lower()->trim()->value();
                $tag = Tag::whereRaw('LOWER(name) = ?', [$lowerTagName])->first();

                if (is_null($tag)) {
                    $tag = Tag::create([
                        'name' => trim($market),
                    ]);
                }

                if (! $tag->wasRecentlyCreated) {
                    $tag->update(['name' => trim($market)]);
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
