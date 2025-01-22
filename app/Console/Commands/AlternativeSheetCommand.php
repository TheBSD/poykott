<?php

namespace App\Console\Commands;

use App\Actions\CreateOrUpdateAlternativeByNameAction;
use App\Actions\CreateOrUpdateCompanyByNameAction;
use App\Actions\FindOrCreateTagByNameAction;
use App\Enums\ResourceType;
use Illuminate\Console\Command;

class AlternativeSheetCommand extends Command
{
    protected $signature = 'import:alternative-sheet';

    public function handle(
        FindOrCreateTagByNameAction $findOrCreateTagByNameAction,
        CreateOrUpdateCompanyByNameAction $createOrUpdateCompanyByNameAction,
        CreateOrUpdateAlternativeByNameAction $createOrUpdateAlternativeByNameAction,
    ): void {
        $json = file_get_contents(database_path('seeders/data/9-israeli-alternatives-sheet.json'));

        $allData = json_decode($json, true);

        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($allData as $data) {
            /**
             * Company create or update
             */
            $companyName = data_get($data, 'AlternateTo');
            $company = $createOrUpdateCompanyByNameAction->execute(
                companyName: $companyName,
                forcedFields: ['approved_at' => now()],
                optionalFields: ['url' => '#']
            );

            /**
             * Alternative create or update
             */
            $alternativeName = data_get($data, 'Alt Name');
            $alternative = $createOrUpdateAlternativeByNameAction->execute(
                alternativeName: $alternativeName,
                forcedFields: [
                    'approved_at' => now(),
                ],
                optionalFields: [
                    'url' => data_get($data, 'Website'),
                    'description' => data_get($data, 'Description'),
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
                'url' => '#',
            ], [
                'type' => ResourceType::FromUsers,
            ]);

            /**
             * Alternative tags
             */
            $tagName = data_get($data, 'Primary Sector');
            $tag = $findOrCreateTagByNameAction->execute($tagName);

            if ($company->doesntHaveTag($tag)) {
                $company->tagsRelation()->attach($tag);
            }

            if ($alternative->doesntHaveTag($tag)) {
                $alternative->tagsRelation()->attach($tag);
            }

            $this->line("Processed importing: {$company->name}\n");
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info("\nProcessed Completed!");
    }
}
