<?php

namespace App\Console\Commands;

use App\Enums\CompanyPersonType;
use App\Enums\ResourceType;
use App\Models\Company;
use App\Models\Investor;
use App\Models\Person;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ImportUnicornTechAvivCommand extends Command
{
    protected $signature = 'import:unicorn-tech-aviv';

    protected $description = 'Command description';

    /**
     * @throws FileCannotBeAdded
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function handle(): void
    {
        $json = file_get_contents(database_path('seeders/data/1-unicorn.json'));

        $allData = json_decode($json, true);

        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($allData as $data) {
            $lowerCompanyName = Str::of(data_get($data, 'Company'))->lower()->trim()->value();

            $company = Company::query()->whereRaw('Lower(name) = ?', [$lowerCompanyName])->first();

            $dataFields = [
                'valuation' => data_get($data, 'Valuation'),
                'url' => data_get($data, 'Website'),
                'total_funding' => data_get($data, 'Total Funding'),
                'last_funding_date' => data_get($data, 'Last Funding'),
                'headquarter' => data_get($data, 'HQ Location'),
                'founded_at' => Carbon::createFromFormat('Y', data_get($data, 'Founded')),
                'description' => data_get($data, 'Description'),
                'approved_at' => now(),
            ];

            if (is_null($company)) {
                $company = Company::query()->create(array_merge([
                    'name' => trim((string) data_get($data, 'Company')),
                ], $dataFields));
            }

            $company->update($dataFields);

            $unicornsUrl = 'https://www.techaviv.com/unicorns';
            $companyResource = $company->resources()->updateOrCreate([
                'url' => $unicornsUrl,
            ], [
                'type' => ResourceType::TechAviv,
            ]);

            $foundersString = data_get($data, 'Israeli Founders');
            $founders = Str::of($foundersString)
                ->chopEnd('...')
                ->explode(',')
                ->reject(fn ($founder): bool => in_array(trim($founder), ['', '0'], true));

            foreach ($founders as $founder) {
                $person = Person::query()->firstOrCreate(
                    [
                        'name' => trim($founder),
                    ],
                    [
                        'job_title' => 'Founder ' . $company->name,
                        'approved_at' => now(),
                    ]);

                if (empty($person->job_title)) {
                    $person->update(['job_title' => 'Founder ' . $company->name]);
                }

                $personResource = $person->resources()->updateOrCreate([
                    'url' => $unicornsUrl,
                ], [
                    'type' => ResourceType::TechAviv,
                ]);

                if ($company->people()->where('person_id', $person->id)->doesntExist()) {
                    $company->people()->attach($person, ['type' => CompanyPersonType::Founder]);
                }
            }

            $investorsString = data_get($data, 'Top Investors');
            $investors = Str::of($investorsString)
                ->explode(',')
                ->reject(fn ($investor): bool => in_array(trim($investor), ['', '0'], true));

            foreach ($investors as $investorData) {

                $lowerInvestorName = Str::of($investorData)->lower()->trim()->value();
                $investor = Investor::query()->whereRaw('LOWER(name) = ?', [$lowerInvestorName])->first();

                if (is_null($investor)) {
                    $investor = Investor::query()->create([
                        'name' => trim($investorData),
                        'approved_at' => now(),
                    ]);
                }

                $resourceResource = $investor->resources()->updateOrCreate([
                    'url' => $unicornsUrl,
                ], [
                    'type' => ResourceType::TechAviv,
                ]);

                if ($company->investors()->where('investor_id', $investor->id)->doesntExist()) {
                    $company->investors()->attach($investor);
                }
            }

            $tagsString = data_get($data, 'Sectors');
            $tags = Str::of($tagsString)
                ->explode(',')
                ->reject(fn ($investor): bool => in_array(trim($investor), ['', '0'], true));

            $tagsIds = [];
            foreach ($tags as $tagData) {
                $lowerTagName = Str::of($tagData)->lower()->trim()->value();
                $tag = Tag::query()->whereRaw('LOWER(name) = ?', [$lowerTagName])->first();

                if (is_null($tag)) {
                    $tag = Tag::query()->create([
                        'name' => trim((string) $tagData),
                    ]);
                }

                $tagsIds[] = $tag->id;
            }
            $company->syncTags($tagsIds);

            $this->line("Processed importing: {$company->name}");
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info("\nProcessed Completed!");

    }
}
