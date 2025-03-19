<?php

namespace App\Console\Commands;

use App\Actions\CreateOrUpdateAlternativeByNameAction;
use App\Actions\FindOrCreateTagByNameAction;
use App\Enums\ResourceType;
use App\Models\Company;
use Illuminate\Console\Command;

class StripeAlternativesCommand extends Command
{
    protected $signature = 'import:stripe-alternatives';

    public function handle(
        FindOrCreateTagByNameAction $findOrCreateTagByNameAction,
        CreateOrUpdateAlternativeByNameAction $createOrUpdateAlternativeByNameAction,
    ): void {
        $json = file_get_contents(database_path('seeders/data/10-payoneer-alternatives.json'));

        $allData = json_decode($json, true);

        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($allData as $data) {
            /**
             * Company create or update
             */
            $company = Company::query()->where('name', 'Payoneer')->first();

            /**
             * Alternative create or update
             */
            $alternativeName = data_get($data, 'title');
            $alternative = $createOrUpdateAlternativeByNameAction->execute(
                alternativeName: $alternativeName,
                forcedFields: [
                    'approved_at' => now(),
                ],
                optionalFields: [
                    'url' => data_get($data, 'link'),
                    'description' => data_get($data, 'description'),
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
                'url' => 'https://stripealternatives.com',
            ], [
                'type' => ResourceType::StripeAlternatives,
            ]);

            /**
             * Alternative tags
             */
            $tagNames = ['Payments', 'Fintech'];
            foreach ($tagNames as $tagName) {
                $tag = $findOrCreateTagByNameAction->execute($tagName);

                if ($alternative->doesntHaveTag($tag)) {
                    $alternative->tagsRelation()->attach($tag);
                }
            }

            $this->line("Processed importing: {$company->name}\n");
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info("\nProcessed Completed!");
    }
}
