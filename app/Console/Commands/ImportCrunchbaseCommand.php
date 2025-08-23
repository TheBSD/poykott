<?php

namespace App\Console\Commands;

use App\Actions\CreateOrUpdateCompanyByNameAction;
use App\Actions\FindOrCreateTagByNameAction;
use App\Enums\CompanyPersonType;
use App\Enums\ResourceType;
use App\Models\Investor;
use App\Models\Person;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportCrunchbaseCommand extends Command
{
    protected $signature = 'import:crunchbase';

    public function handle(
        FindOrCreateTagByNameAction $findOrCreateTagByNameAction,
        CreateOrUpdateCompanyByNameAction $createOrUpdateCompanyByNameAction,
    ): void {
        $json = file_get_contents(database_path('seeders/data/13-crunchbase.json'));

        $allData = json_decode($json, true);
        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($allData as $data) {

            $reason = data_get($data, 'reasons');

            /**
             * Filter companies that are only just investors
             */
            if (count($reason) == 1 && $reason[0] == 'i') {
                continue;
            }

            $this->line('Process importing: ' . data_get($data, 'name'));

            /**
             * Useful data
             */
            $companyName = data_get($data, 'name');
            $companyStockSymbol = data_get($data, 'stock_symbol');
            $companyDescription = data_get($data, 'description');
            $companyUrl = data_get($data, 'ws');
            $companySocialLinks = collect([
                data_get($data, 'li'),
                data_get($data, 'fb'),
                data_get($data, 'tw'),
            ])->filter()->toArray();
            $companyTags = data_get($data, 'industries');
            $companyCrunchbaseLink = data_get($data, 'cbLink');
            $companyFounders = data_get($data, 'founderIds');
            $companyInvestors = data_get($data, 'investorIds');

            /**
             * Company fields
             */
            $company = $createOrUpdateCompanyByNameAction->execute(
                companyName: $companyName,
                optionalFields: [
                    'approved_at' => now(),
                    'url' => $companyUrl,
                    'description' => $companyDescription,
                    'stock_symbol' => $companyStockSymbol,
                ]
            );

            /**
             * Company tags
             */
            foreach ($companyTags as $companyTag) {
                $tag = $findOrCreateTagByNameAction->execute($companyTag);

                if ($company->doesntHaveTag($tag)) {
                    $company->tagsRelation()->attach($tag);
                }
            }

            /**
             * Company resources
             */
            $company->resources()->updateOrCreate([
                'url' => $companyCrunchbaseLink,
            ], [
                'type' => ResourceType::Crunchbase,
            ]);

            /**
             * Company social links
             */
            foreach ($companySocialLinks as $socialLink) {
                $company->socialLinks()->updateOrCreate([
                    'url' => $socialLink,
                ], []);
            }

            /**
             * Company founders
             */
            foreach ($companyFounders as $founder) {
                $person = Person::query()->firstOrCreate(
                    [
                        'name' => trim((string) data_get($founder, 'name')),
                    ],
                    [
                        'job_title' => 'Founder ' . $company->name,
                        'approved_at' => now(),
                    ]);

                if (empty($person->job_title)) {
                    $person->update(['job_title' => 'Founder ' . $company->name]);
                }

                $person->resources()->updateOrCreate([
                    'url' => data_get($founder, 'link'),
                ], [
                    'type' => ResourceType::Crunchbase,
                ]);

                if ($company->people()->where('person_id', $person->id)->doesntExist()) {
                    $company->people()->attach($person, ['type' => CompanyPersonType::Founder]);
                }
            }

            /**
             * Company investors
             */
            foreach ($companyInvestors as $companyInvestor) {
                $investorName = data_get($companyInvestor, 'name');
                $lowerName = Str::of($investorName)->lower()->squish()->value();
                $investor = Investor::query()->whereRaw('LOWER(name) = ?', [$lowerName])->first();

                if (is_null($investor)) {
                    $investor = Investor::query()->create([
                        'name' => Str::of($investorName)->squish()->value(),
                        'approved_at' => now(),
                    ]);
                }

                $investor->resources()->updateOrCreate([
                    'url' => data_get($companyInvestor, 'link'),
                ], [
                    'type' => ResourceType::Crunchbase,
                ]);

                if ($company->investors()->where('investor_id', $investor->id)->doesntExist()) {
                    $company->investors()->attach($investor);
                }
            }

            $this->info("Processed {$company->name} completed\n");
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info("\nProcessed Completed!");
    }
}
