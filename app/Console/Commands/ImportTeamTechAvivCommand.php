<?php

namespace App\Console\Commands;

use App\Enums\CompanyPersonType;
use App\Models\Company;
use App\Models\Person;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportTeamTechAvivCommand extends Command
{
    protected $signature = 'import:team-tech-aviv';

    protected $description = 'Command description';

    public function handle(): void
    {
        $json = file_get_contents(storage_path('app/private/0-team.json'));

        $allData = json_decode($json, true);

        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($allData as $data) {
            $person = Person::updateOrCreate([
                'name' => data_get($data, 'name'),
            ], [
                'url' => data_get($data, 'url'),
                'avatar' => data_get($data, 'avatar'),
                'job_title' => data_get($data, 'title'),
                'location' => data_get($data, 'location'),
                'description' => data_get($data, 'description'),
                'social_links' => data_get($data, 'socials'),
            ]);

            /**
             * These Are job titles that their companies are not necessary
             * israeli companies, so we ignore adding these companies to
             * make the data accurate
             */
            if (in_array(data_get($data, 'title'), [
                'CISO', 'Investor', 'General Partner', 'GM, Google Cloud', 'VP Engineering',
                'VP Engineering, Search & AI', 'Senior Engineering Director', 'Senior Director of Engineering',
                'Fmr. VP Global Sales & Operation', 'Professor', 'GM', 'SVP Real Time Operations, Head of European R&D',
                'Growth Partner', 'Head of WorldWide Innovation', 'EVP Product & Strategy', 'Director of Product Management',
                'President of Technology', 'CEO, Uber Freight', 'Founder & Managing Partner', 'Managing Director',
                'VP & GM, Opendoor Exclusives', 'Chief Digital Officer', 'Fmr. VP Global Sales & Operations', 'GM, Caviar',
                'VP, Trust & Safety', 'VP Applications', 'Chairman Itaú Latin America', 'Chief Public Affairs Officer',
                'Founder & VP R&', 'Senior Managing Director',
                'SVP Real Time Operations, Head of European R&D', 'Founder & VP Customer Success', 'General Partner',
            ]) && data_get($data, 'location') != 'Israel') {
                continue;
            }

            $lowerCompanyName = Str::of(data_get($data, 'company.name'))->lower()->trim()->value();

            $company = Company::whereRaw('LOWER(name) = ?', [$lowerCompanyName])->first();

            if (is_null($company)) {
                $company = Company::create([
                    'name' => trim(data_get($data, 'company.name')),
                    'url' => data_get($data, 'company.link'),
                    'logo' => data_get($data, 'company.logo'),
                ]);
            }

            if (empty($company->url)) {
                $company->update(['url' => data_get($data, 'company.url')]);
            }

            if (empty($company->logo)) {
                $company->update(['logo' => data_get($data, 'company.url')]);
            }

            $companyPersonType = null;

            $mainCategory = ImportPortfolioTechAvivCommand::companyPersonCategories();

            foreach ($mainCategory as $category => $value) {
                foreach ($value as $personCategory) {
                    if (trim($person->job_title) == $personCategory) {
                        $companyPersonType = match ($category) {
                            'Founder' => CompanyPersonType::Founder,
                            'Investment' => CompanyPersonType::Investor,
                            'Executive' => CompanyPersonType::Executive,
                            'Operational' => CompanyPersonType::Operational,
                            'Senior Management' => CompanyPersonType::SeniorManager,
                            'Academic' => CompanyPersonType::Academic,
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
