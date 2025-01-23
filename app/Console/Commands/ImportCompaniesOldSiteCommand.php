<?php

namespace App\Console\Commands;

use App\Actions\CreateOrUpdateCompanyByNameAction;
use App\Enums\ResourceType;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportCompaniesOldSiteCommand extends Command
{
    protected $signature = 'import:companies-old-site';

    protected $description = 'Command description';

    public function handle(
        CreateOrUpdateCompanyByNameAction $createOrUpdateCompanyByNameAction
    ): void {
        $json = file_get_contents(database_path('seeders/data/7-israel-companies-services.json'));

        $allData = json_decode($json, true);

        $progressBar = $this->output->createProgressBar(count($allData));

        $companies = data_get($allData, 'companiesAndServices');

        foreach ($companies as $companyData) {
            /**
             * Company create or update
             */
            $companyName = Str::lower(data_get($companyData, 'name'));
            $company = $createOrUpdateCompanyByNameAction->execute(
                companyName: $companyName,
                forcedFields: ['approved_at' => now()],
                optionalFields: [
                    'description' => data_get($companyData, 'description'),
                    'url' => '#',
                ]
            );

            $resourcesData = data_get($companyData, 'resources');

            foreach ($resourcesData as $resourceData) {

                if (empty(data_get($resourceData, 'name'))) {
                    continue;
                }

                $companyResource = $company->resources()->updateOrCreate([
                    'url' => data_get($resourceData, 'link', '#'),
                ], [
                    'type' => match (data_get($resourceData, 'name')) {
                        'Wikipedia' => ResourceType::Wikipedia,
                        'Twitter' => ResourceType::Twitter,
                        'LinkedIn' => ResourceType::LinkedIn,
                        'Wikitia' => ResourceType::Wikitia,
                        'Wikidata' => ResourceType::Wikidata,
                        'golden.com' => ResourceType::Golden,
                        'verify.wiki' => ResourceType::VerifyWiki,
                        'Buy Israeli Tech' => ResourceType::BuyIsraeliTech,
                        'عن الموقع' => ResourceType::OfficialWebsite,
                        'bloomberg' => ResourceType::Bloomberg,
                    },
                ]);
            }

            $alternativesData = data_get($companyData, 'alternatives');

            foreach ($alternativesData as $alternativeData) {
                if (empty($alternativeData['name'])) {
                    continue;
                }
                $alternative = $company->alternatives()->updateOrCreate([
                    'name' => data_get($alternativeData, 'name'),
                ], [
                    'description' => data_get($alternativeData, 'description'),
                    'url' => data_get($alternativeData, 'url', '#'),
                    'notes' => data_get($alternativeData, 'notes'),
                    'approved_at' => now(),
                ]);

                $alternativeResource = $alternative->resources()->updateOrCreate([
                    'url' => $alternative->url,
                ], [
                    'type' => ResourceType::FromUsers,
                ]);
            }

            $this->line("Processed importing: {$company->name}");
            $progressBar->advance();

            $progressBar->finish();

            $this->info("\nProcessed Completed!");
        }

    }
}
