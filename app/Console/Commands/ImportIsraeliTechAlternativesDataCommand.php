<?php

namespace App\Console\Commands;

use App\Actions\CreateOrUpdateAlternativeByNameAction;
use App\Actions\CreateOrUpdateCompanyByNameAction;
use App\Actions\FindOrCreateTagByNameAction;
use App\Enums\ResourceType;
use Illuminate\Console\Command;

class ImportIsraeliTechAlternativesDataCommand extends Command
{
    protected $signature = 'import:israeli-tech-alternatives';

    public function handle(
        FindOrCreateTagByNameAction $findOrCreateTagByNameAction,
        CreateOrUpdateCompanyByNameAction $createOrUpdateCompanyByNameAction,
        CreateOrUpdateAlternativeByNameAction $createOrUpdateAlternativeByNameAction,
    ): void {
        $json = file_get_contents(database_path('seeders/data/11-israelitechalternatives.json'));

        $allData = json_decode($json, true);

        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($allData as $data) {

            /**
             * Company create or update
             */
            $companyName = data_get($data, 'Name');
            $companyDescription = data_get($data, 'Description');
            $companyUrl = data_get($data, 'Website');
            $companyImageUrl = data_get($data, 'url');
            $companyTag = data_get($data, 'Category');

            $company = $createOrUpdateCompanyByNameAction->execute(
                companyName: $companyName,
                forcedFields: ['approved_at' => now()],
                optionalFields: [
                    'url' => $companyUrl,
                    'description' => $companyDescription,
                ]
            );

            /**
             * Add company temp media
             */
            $company->addTempMedia($companyImageUrl);

            /**
             * Company tag
             */
            $tag = $findOrCreateTagByNameAction->execute($companyTag);

            if ($company->doesntHaveTag($tag)) {
                $company->tagsRelation()->attach($tag);
            }

            /**
             * Alternative create or update
             */
            $alternativeArray = array_merge(
                data_get($data, 'alternative', []),
                data_get($data, 'Alternatives2', []),
            );

            foreach ($alternativeArray as $alternativeItem) {
                $alternativeName = data_get($alternativeItem, 'Name');
                $alternativeDescription = data_get($alternativeItem, 'Description');
                $alternativeUrl = data_get($alternativeItem, 'Website');
                $alternativeImageUrl = data_get($alternativeItem, 'url');

                $alternative = $createOrUpdateAlternativeByNameAction->execute(
                    alternativeName: $alternativeName,
                    forcedFields: [
                        'approved_at' => now(),
                    ],
                    optionalFields: [
                        'url' => $alternativeUrl,
                        'description' => $alternativeDescription,
                        'notes' => data_get($alternativeItem, 'IsMENA') ? 'is_mena' : null,
                    ]
                );

                /**
                 * Alternative association with company
                 */
                $alternative->companies()->syncWithoutDetaching($company);

                /**
                 * Alternative resource create or update
                 */
                $alternative->resources()->updateOrCreate([
                    'url' => 'https://www.israelitechalternatives.com',
                ], [
                    'type' => ResourceType::IsraeliTechAlternatives,
                ]);

                /**
                 * Add alternative temp media
                 */
                if ($alternativeImageUrl) {
                    $alternative->addTempMedia($alternativeImageUrl);
                }
            }

            $this->line("Processed importing: {$company->name}\n");
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info("\nProcessed Completed!");
    }
}
