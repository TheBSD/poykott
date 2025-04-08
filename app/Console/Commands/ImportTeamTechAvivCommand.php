<?php

namespace App\Console\Commands;

use App\Enums\CompanyPersonType;
use App\Enums\ResourceType;
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
        $json = file_get_contents(database_path('seeders/data/0-team.json'));

        $allData = json_decode($json, true);

        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($allData as $data) {
            $person = Person::query()->updateOrCreate([
                'name' => data_get($data, 'name'),
            ], [
                'url' => data_get($data, 'url'),
                'job_title' => data_get($data, 'title'),
                'location' => data_get($data, 'location'),
                'description' => data_get($data, 'description'),
                'social_links' => data_get($data, 'socials'),
                'approved_at' => now(),
            ]);

            add_image_urls_to_notes(data_get($data, 'avatar'), $person, $this);

            // if($person->wasRecentlyCreated) {
            //    $imagePath = get_image_archive_path(data_get($data, 'avatar'), 'people');
            //
            //    if (!add_image_for_model($imagePath, $person)) {
            //        dump("Failed to add image to model ".get_class($person).":".$person->id);
            //
            //        if (Str::isUrl(data_get($data, 'avatar'))) {
            //            dump("\n Try to Download it from Url..");
            //            $person->addMediaFromUrl(data_get($data, 'avatar'));
            //        }
            //    }
            // }

            $personResource = $person->resources()->updateOrCreate([
                'url' => $person->url,
            ], [
                'type' => ResourceType::TechAviv,
            ]);

            /**
             * These Are job titles that their companies are not necessary
             * israeli companies, so we ignore adding these companies to
             * make the data accurate
             */
            if (in_array(data_get($data, 'title'), [
                'CISO', 'Investor', 'General Partner', 'GM, Google Cloud', 'VP Engineering',
                'VP Engineering, Search & AI', 'Senior Engineering Director', 'Senior Director of Engineering',
                'Fmr. VP Global Sales & Operation', 'Professor', 'GM',
                'SVP Real Time Operations, Head of European R&D',
                'Growth Partner', 'Head of WorldWide Innovation', 'EVP Product & Strategy',
                'Director of Product Management',
                'President of Technology', 'CEO, Uber Freight', 'Founder & Managing Partner', 'Managing Director',
                'VP & GM, Opendoor Exclusives', 'Chief Digital Officer', 'Fmr. VP Global Sales & Operations',
                'GM, Caviar',
                'VP, Trust & Safety', 'VP Applications', 'Chairman Itaú Latin America',
                'Chief Public Affairs Officer',
                'Founder & VP R&', 'Senior Managing Director',
                'SVP Real Time Operations, Head of European R&D', 'Founder & VP Customer Success',
                'General Partner',
            ]) && data_get($data, 'location') != 'Israel') {
                continue;
            }

            $lowerCompanyName = Str::of(data_get($data, 'company.name'))->lower()->trim()->value();

            $company = Company::query()->whereRaw('LOWER(name) = ?', [$lowerCompanyName])->first();

            if (is_null($company)) {
                $company = Company::query()->create([
                    'name' => trim((string) data_get($data, 'company.name')),
                    'url' => data_get($data, 'company.link'),
                    'approved_at' => now(),
                ]);

                // $company->update(['notes' => 'img:'.data_get($data, 'company.logo')]);
                add_image_urls_to_notes(data_get($data, 'company.logo'), $company, $this);

                // $companyImagePath = get_image_archive_path(data_get($data, 'company.logo'), 'companies');
                //
                // if (!add_image_for_model($companyImagePath, $company)) {
                //    dump("Failed to add image to model ".get_class($company).":".$company->id);
                //
                //    if (Str::isUrl(data_get($data, 'company.logo'))) {
                //        dump("\n Try to Download it from Url..");
                //        $person->addMediaFromUrl(data_get($data, 'company.logo'));
                //    }
                // }
            }

            if (empty($company->url)) {
                $company->update(['url' => data_get($data, 'company.link')]);
            }

            $companyResource = $company->resources()->updateOrCreate([
                'url' => $person->url,
            ], [
                'type' => ResourceType::TechAviv,
            ]);

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
