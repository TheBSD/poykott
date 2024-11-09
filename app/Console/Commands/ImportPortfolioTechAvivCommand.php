<?php

namespace App\Console\Commands;

use App\Enums\CompanyPersonType;
use App\Enums\ResourceType;
use App\Models\Company;
use App\Models\Person;
use Illuminate\Console\Command;

class ImportPortfolioTechAvivCommand extends Command
{
    protected $signature = 'import:portfolio-tech-aviv';

    protected $description = 'Command description';

    public function handle(): void
    {
        $json = file_get_contents(storage_path('app/private/3-portfolio.json'));

        $allData = json_decode($json, true);

        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($allData as $data) {

            $company = Company::updateOrCreate(
                [
                    'name' => \str(data_get($data, 'name'))->lower()->value(),
                ],
                [
                    'url' => data_get($data, 'link') ?? data_get($data, 'url'),
                    'description' => data_get($data, 'description'),
                    'short_description' => data_get($data, 'short_description'),
                    'logo' => data_get($data, 'logo'),
                ]
            );

            $companyResource = $company->companyResources()->firstOrCreate([
                'url' => data_get($data, 'url'),
            ], [
                'title' => ResourceType::TechAviv,
            ]);

            if (empty($companyResource->title)) {
                $companyResource->update(['title' => ResourceType::TechAviv]);
            }

            $company->resources()->updateOrCreate([
                'url' => data_get($data, 'url'),
            ], [
                'type' => ResourceType::TechAviv,
            ]);

            $founders = data_get($data, 'founders');

            foreach ($founders as $founder) {
                $person = Person::updateOrCreate(
                    ['full_name' => trim(data_get($founder, 'name'))],
                    [
                        'job_title' => trim(data_get($founder, 'title')),
                        'avatar' => data_get($founder, 'avatar'),
                    ]
                );

                $companyPersonType = null;

                $mainCategory = self::companyPersonCategories();

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

                if ($company->people()->where('person_id', $person->id)->doesntExist()) {
                    $company->people()->attach($person, ['type' => $companyPersonType]);
                } elseif ($company->people->first()->pivot->type != $companyPersonType) {
                    $company->people()->updateExistingPivot($person->id, ['type' => $companyPersonType]);
                }
            }

            $stats = data_get($data, 'stats');

            foreach ($stats as $stat) {
                if (data_get($stat, 'value') == 'Founded') {
                    $date = \Carbon\Carbon::createFromFormat('Y', data_get($stat, 'key'));
                    $company->update(['founded_at' => $date]);
                }
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
                'CEO, Uber Freight',
                'CTO Coach',
                'CFO'
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
                'Founding Partner',
                'Founder and Managing Partner',
                'Founder and COO',
                'Founder & Partner',
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
                'Global Head of Music',
                'VP Engineering, Search & AI',
                'VP, Product',
                'GM',
                'Growth Partner',
                'Head of WorldWide Innovation',
                'Director of Product Management'
            ],
            'Operational' => [
                'GM, Caviar',
                'GM, Google Cloud',
                'Senior Engineering Director',
                'Fmr. VP Global Sales & Operations',
                'SVP Real Time Operations, Head of European R&D',
                'Senior Director of Engineering'
            ],
            'Investment' => [
                'Investor',
                'Venture Partner',
                'Angel Investor',
            ],
            'Academic' => [
                'Professor'
            ]
        ];

        return $categories;
    }
}
