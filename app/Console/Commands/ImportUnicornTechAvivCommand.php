<?php

namespace App\Console\Commands;

use App\Enums\CompanyPersonType;
use App\Enums\ResourceType;
use App\Models\Company;
use App\Models\Investor;
use App\Models\Person;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportUnicornTechAvivCommand extends Command
{
    protected $signature = 'import:unicorn-tech-aviv';

    protected $description = 'Command description';

    public function handle(): void
    {
        $json = file_get_contents(storage_path('app/private/1-unicorn.json'));

        $allData = json_decode($json, true);

        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($allData as $data) {
            $lowerCompanyName = Str::of(data_get($data, 'Company'))->lower()->trim()->value();

            $company = Company::whereRaw('Lower(name) = ?', [$lowerCompanyName])->first();

            $dataFields = [
                'valuation' => data_get($data, 'Valuation'),
                'url' => data_get($data, 'Website'),
                'total_funding' => data_get($data, 'Total Funding'),
                'last_funding_date' => data_get($data, 'Last Funding'),
                'headquarter' => data_get($data, 'HQ Location'),
                'founded_at' => \Carbon\Carbon::createFromFormat('Y', data_get($data, 'Founded')),
                'description' => data_get($data, 'Description'),
            ];
            if (is_null($company)) {
                $company = Company::create(array_merge([
                    'name' => trim(data_get($data, 'Company')),
                ], $dataFields));
            }

            $company->update($dataFields);

            $unicornsUrl = 'https://www.techaviv.com/unicorns';
            $companyResource = $company->resources()->updateOrCreate([
                'url' => $unicornsUrl,
            ], [
                'type' => ResourceType::TechAviv,
            ]);

            $foundersString = data_get($data, 'Israeli Founders');
            $founders = Str::of($foundersString)
                ->chopEnd('...')
                ->explode(',')
                ->reject(fn ($founder) => empty(trim($founder)));

            foreach ($founders as $founder) {
                $person = Person::firstOrCreate(
                    ['name' => trim($founder)],
                    ['job_title' => 'Founder '.$company->name]
                );

                if (empty($person->job_title)) {
                    $person->update(['job_title' => 'Founder '.$company->name]);
                }

                $personResource = $person->resources()->updateOrCreate([
                    'url' => $unicornsUrl,
                ], [
                    'type' => ResourceType::TechAviv,
                ]);

                if ($company->people()->where('person_id', $person->id)->doesntExist()) {
                    $company->people()->attach($person, ['type' => CompanyPersonType::Founder]);
                }
            }

            $investorsString = data_get($data, 'Top Investors');
            $investors = Str::of($investorsString)
                ->explode(',')
                ->reject(fn ($investor) => empty(trim($investor)));

            foreach ($investors as $investorData) {

                $lowerInvestorName = Str::of($investorData)->lower()->trim()->value();
                $investor = Investor::whereRaw('LOWER(name) = ?', [$lowerInvestorName])->first();

                if (is_null($investor)) {
                    $investor = Investor::create([
                        'name' => trim($investorData),
                    ]);
                }

                $resourceResource = $investor->resources()->updateOrCreate([
                    'url' => $unicornsUrl,
                ], [
                    'type' => ResourceType::TechAviv,
                ]);

                if ($company->investors()->where('investor_id', $investor->id)->doesntExist()) {
                    $company->investors()->attach($investor);
                }
            }

            $tagsString = data_get($data, 'Sectors');
            $tags = \Str::of($tagsString)
                ->explode(',')
                ->reject(fn ($investor) => empty(trim($investor)));

            $tagsIds = [];
            foreach ($tags as $tagData) {
                $lowerTagName = Str::of($tagData)->lower()->trim()->value();
                $tag = \App\Models\Tag::whereRaw('LOWER(name) = ?', [$lowerTagName])->first();

                if (is_null($tag)) {
                    $tag = \App\Models\Tag::create([
                        'name' => trim($tagData),
                    ]);
                }

                $tagsIds[] = $tag->id;
            }
            $company->syncTags($tagsIds);

            $this->line("Processed importing: {$company->name}");
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info("\nProcessed Completed!");

    }
}
