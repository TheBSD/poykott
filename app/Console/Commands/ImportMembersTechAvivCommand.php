<?php

namespace App\Console\Commands;

use App\Enums\CompanyPersonType;
use App\Models\Company;
use App\Models\Person;
use Illuminate\Console\Command;

class ImportMembersTechAvivCommand extends Command
{
    protected $signature = 'import:members-tech-aviv';

    protected $description = 'Command description';

    public function handle(): void
    {
        $json = file_get_contents(storage_path('app/private/4-members.json'));

        $allData = json_decode($json, true);

        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($allData as $data) {
            $person = Person::updateOrCreate([
                'full_name' => data_get($data, 'name'),
            ], [
                'url' => data_get($data, 'url'),
                'avatar' => data_get($data, 'avatar'),
                'job_title' => data_get($data, 'title'),
                'location' => data_get($data, 'location'),
                'description' => data_get($data, 'description'),
                'social_links' => data_get($data, 'socials'),
            ]);

            if (! data_get($data, 'company.name')) {
                continue;
            }

            $company = Company::updateOrCreate([
                'name' => \str(data_get($data, 'company.name'))->lower()->value(),
            ], [
                'url' => data_get($data, 'company.link'),
                'logo' => data_get($data, 'company.logo'),
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

    /**
     * @return array<string>
     */
    public static function companyPersonCategories(): array
    {
        $categories = [
            'Executive' => [
                'CEO',
                'Co-founder & CEO',
                'Founder & CEO',
                'President of Technology',
                'Founder & Chairman',
                'Co-founder and Chairman',
                'Chairman',
                'Managing Director',
                'Managing Partner',
                'General Partner',
                'Senior Managing Director',
                'Partner',
                'Chairman Itaú Latin America',
                'President Israel',
            ],
            'Founder' => [
                'Founder',
                'Founder & COO',
                'Founder & CPO',
                'Founder & President',
                'Founder & CTO',
                'Founder & CSO',
                'Founder & CIO',
                'Founder & Managing Partner',
                'Founder & VP R&D',
                'Founder & Chief Research & Innovation Officer',
                'Founder & Director',
                'Founder & VP Product',
                'Founder & Director of Engineering',
                'Founder & CMO',
                'Founder & VP Customer Success',
                'Founder & Chief Architect',
                'Founder & CFO',
                'Founder & CBO',
            ],
            'Senior Management' => [
                'VP Engineering',
                'VP & GM, Opendoor Exclusives',
                'VP, Trust & Safety',
                'VP Applications',
                'VP Product',
                'CPO',
                'Chief Digital Officer',
                'CISO',
                'Chief Public Affairs Officer',
                'EVP Product & Strategy',
            ],
            'Operational' => [
                'GM, Caviar',
                'GM, Google Cloud',
                'Senior Engineering Director',
                'Fmr. VP Global Sales & Operations',
                'SVP Real Time Operations, Head of European R&D',
            ],
            'Investment' => [
                'Investor',
                'Venture Partner',
                'Angel Investor',
            ],
        ];

        return $categories;
    }
}
