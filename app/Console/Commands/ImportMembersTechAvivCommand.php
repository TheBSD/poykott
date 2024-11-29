<?php

namespace App\Console\Commands;

use App\Enums\CompanyPersonType;
use App\Enums\ResourceType;
use App\Models\Company;
use App\Models\Person;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportMembersTechAvivCommand extends Command
{
    protected $signature = 'import:members-tech-aviv';

    protected $description = 'Command description';

    public function handle(): void
    {
        $json = file_get_contents(database_path('seeders/data/4-members.json'));

        $allData = json_decode($json, true);

        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($allData as $data) {
            $person = Person::query()->updateOrCreate([
                'name' => data_get($data, 'name'),
            ], [
                'url' => data_get($data, 'url'),
                'avatar' => data_get($data, 'avatar'),
                'job_title' => data_get($data, 'title'),
                'location' => data_get($data, 'location'),
                'description' => data_get($data, 'description'),
                'social_links' => data_get($data, 'socials'),
            ]);

            $personResource = $person->resources()->updateOrCreate([
                'url' => data_get($data, 'url'),
            ], [
                'type' => ResourceType::TechAviv,
            ]);

            if (! data_get($data, 'company.name')) {
                continue;
            }

            /**
             * These Are job titles that their companies are not necessary
             * israeli companies, so we ignore adding these companies to
             * make the data accurate
             */
            if (in_array(data_get($data, 'title'), [
                'CISO', 'Investor', 'General Partner', 'GM, Google Cloud',
            ]) && data_get($data, 'location') != 'Israel') {
                continue;
            }

            $dataFields = [
                'url' => data_get($data, 'company.link'),
                //'logo' => data_get($data, 'company.logo'),
            ];
            $companyLowerName = Str::of(data_get($data, 'company.name'))->lower()->trim()->value();
            $company = Company::query()->whereRaw('Lower(name)  = ?', [$companyLowerName])->first();

            if (is_null($company)) {
                $company = Company::query()->create(array_merge([
                    'name' => trim((string) data_get($data, 'company.name')),
                ], $dataFields));

                $company->logo()->create([
                    'path' => data_get($data, 'company.logo'),
                ]);
            }

            if (! $company->wasRecentlyCreated) {
                $company->update($dataFields);
            }

            $companyResource = $company->resources()->updateOrCreate([
                'url' => data_get($data, 'url'),
            ], [
                'type' => ResourceType::TechAviv,
            ]);

            $companyPersonType = null;

            $mainCategory = ImportPortfolioTechAvivCommand::companyPersonCategories();

            foreach ($mainCategory as $category => $value) {
                foreach ($value as $personCategory) {
                    if ($person->job_title == $personCategory) {
                        $companyPersonType = match ($category) {
                            'Founder' => CompanyPersonType::Founder,
                            'Investment' => CompanyPersonType::Investor,
                            'Executive' => CompanyPersonType::Executive,
                            'Operational' => CompanyPersonType::Operational,
                            'Senior Management' => CompanyPersonType::SeniorManager,
                        };
                    }
                }
            }

            if (! $company->people()->where('person_id', $person->id)->exists()) {
                $company->people()->attach($person->id, ['type' => $companyPersonType]);
            }

            $this->line("Processed importing: {$company->name}");
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info("\nProcessed Completed!");

    }
}
