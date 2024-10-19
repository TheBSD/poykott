<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\ExitStrategy;
use App\Models\Investor;
use App\Models\Person;
use Illuminate\Console\Command;

class ImportUnicornGraduatesTechAvivCommand extends Command
{
    protected $signature = 'import:unicorn-graduates-tech-aviv';

    protected $description = 'Command description';

    public function handle(): void
    {
        $json = file_get_contents(storage_path('app/private/unicorn-graduates.json'));

        $allData = json_decode($json, true);

        $progressBar = $this->output->createProgressBar(count($allData));

        foreach ($allData as $data) {
            $company = Company::updateOrCreate(
                ['name' => data_get($data, 'Company')],
                [
                    'exit_valuation' => data_get($data, 'Valuation at Exit'),
                    'url' => data_get($data, 'Website'),
                    'total_funding' => data_get($data, 'Total Funding'),
                    'headquarter' => data_get($data, 'HQ'),
                    'founded_at' => \Carbon\Carbon::createFromFormat('Y', data_get($data, 'Founded')),
                    'exit_strategy_id' => ExitStrategy::query()
                        ->where('title', data_get($data, 'Exit'))
                        ->firstOrCreate(['title' => data_get($data, 'Exit')])->id,
                    'stock_symbol' => data_get($data, 'Stock Symbol or Acquirer'),
                    'description' => data_get($data, 'Description'),
                    'last_funding_date' => data_get($data, 'Last Funding'),
                    'stock_quote' => data_get($data, 'Stock Qoute'),
                ]);

            $foundersString = data_get($data, 'Founders');
            $founders = \Str::of($foundersString)
                ->chopEnd('...')
                ->explode(',')
                ->reject(fn ($founder) => empty(trim($founder)));

            foreach ($founders as $founder) {
                $person = Person::updateOrCreate([
                    'full_name' => $founder,
                ], [
                    'job_title' => 'Founder '.$company->name,
                ]);

                if ($company->founders()->where('person_id', $person->id)->doesntExist()) {
                    $company->founders()->attach($person, ['type' => 'founder']);
                }
            }

            $investorsString = data_get($data, 'Top Investors');
            $investors = \Str::of($investorsString)
                ->explode(',')
                ->reject(fn ($investor) => empty(trim($investor)));

            foreach ($investors as $investor) {
                $investor = Investor::updateOrCreate([
                    'name' => $investor,
                ]);

                if ($company->investors()->where('investor_id', $investor->id)->doesntExist()) {
                    $company->investors()->attach($investor);
                }
            }

            $tagsString = data_get($data, 'Sectors');
            $tags = \Str::of($tagsString)
                ->explode(',')
                ->reject(fn ($investor) => empty(trim($investor)));

            $tagsIds = [];
            foreach ($tags as $tag) {
                $tag = \App\Models\Tag::updateOrCreate([
                    'name' => $tag,
                ]);

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
