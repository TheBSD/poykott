<?php

namespace App\Console\Commands;

use App\Actions\CheckAccentedCharacterAction;
use App\Actions\ConvertFormattedNumberToInteger;
use App\Actions\CreateOrUpdateCompanyByNameAction;
use App\Actions\FindOrCreateTagByNameAction;
use App\Enums\ResourceType;
use App\Models\OfficeLocation;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ImportStartupNationFinderCommand extends Command
{
    protected $signature = 'import:startup-nation-finder';

    public function handle(
        FindOrCreateTagByNameAction $findOrCreateTagByNameAction,
        CreateOrUpdateCompanyByNameAction $createOrUpdateCompanyByNameAction,
        ConvertFormattedNumberToInteger $convertFormattedNumberToInteger,
    ): void {
        $json = file_get_contents(database_path('seeders/data/12-startup-nation-companies.json'));

        $allData = json_decode($json, true);

        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($allData as $data) {

            $this->line('Process importing: ' . data_get($data, 'company_name'));

            /**
             * Company create or update
             */
            $companyName = data_get($data, 'company_name');
            $companyShortDescription = data_get($data, 'summary');
            $companyDescription = data_get($data, 'overview');
            $companyImageUrl = data_get($data, 'logo');
            $companyUrl = data_get($data, 'website');
            $companyTag = data_get($data, 'sector');
            $companyFounded = data_get($data, 'founded');
            $companyEmployeeCount = data_get($data, 'num_employees');
            $companyResource = data_get($data, 'url');
            $companySocialLinks = collect([
                data_get($data, 'linked_in'),
                data_get($data, 'facebook'),
                data_get($data, 'twitter'),
                data_get($data, 'instagram'),
                data_get($data, 'youtube'),
            ])->filter()->toArray();
            $companyFundingStage = data_get($data, 'funding_stage');
            $companyTotalFunding = $convertFormattedNumberToInteger->execute(
                data_get($data, 'total_funding')
            );
            $companyLocations = collect([
                Str::of(data_get($data, 'address_in_occupied_palestine'))
                    ->replace('\n', '')
                    ->squish()
                    ->value(),
                Str::of(data_get($data, 'offices_abroad'))
                    ->replace('\n', '')
                    ->squish()
                    ->value(),
            ])->filter()->toArray();
            $company = $createOrUpdateCompanyByNameAction->execute(
                companyName: $companyName,
                forcedFields: [
                    'approved_at' => now(),
                    'short_description' => $companyShortDescription,
                    'founded_at' => $companyFounded ? Carbon::createFromFormat('n/Y',
                        $companyFounded)->format('Y-m-d') : null,
                    'employee_count' => $companyEmployeeCount,
                    'funding_stage' => $companyFundingStage,
                    'total_funding' => $companyTotalFunding,
                ],
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
            $tag = filled($companyTag) ? $findOrCreateTagByNameAction->execute($companyTag) : null;

            if ($tag instanceof Tag && $company->doesntHaveTag($tag)) {
                $company->tagsRelation()->attach($tag);
            }

            /**
             * Company resource create or update
             */
            $company->resources()->updateOrCreate([
                'url' => $companyResource,
            ], [
                'type' => ResourceType::StartupNationFinder,
            ]);

            /**
             * Company locations create or update
             */
            foreach ($companyLocations as $location) {

                if (app(CheckAccentedCharacterAction::class)->execute($location)) {
                    $trimmedName = Str::trim($location);

                    $officeLocation = OfficeLocation::query()
                        ->where('name', $trimmedName)
                        ->first();
                } else {
                    $locationLowerName = Str::of($location)
                        ->lower()
                        ->value();

                    $officeLocation = OfficeLocation::query()
                        ->whereRaw('LOWER(name) = ?', strtolower($locationLowerName))
                        ->first();
                }

                if (is_null($officeLocation)) {
                    $officeLocation = OfficeLocation::query()->create([
                        'name' => Str::of($location)->squish()->value(),
                    ]);
                }

                $company->officeLocations()->syncWithoutDetaching([$officeLocation->id]);
            }

            /**
             * Company social links create or update
             */
            foreach ($companySocialLinks as $socialLink) {
                $company->socialLinks()->updateOrCreate([
                    'url' => $socialLink,
                ], []);
            }

            $this->info("Processed {$company->name} completed\n");
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info("\nProcessed Completed!");
    }
}
