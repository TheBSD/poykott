<?php

namespace App\Console\Commands;

use App\Models\Alternative;
use App\Models\Company;
use App\Models\Investor;
use App\Models\Person;
use Illuminate\Console\Command;

class ApproveOurDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:approve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $progressBar = $this->output->createProgressBar(4);

        $innerProgressBar = $this->output->createProgressBar(Company::query()->count());
        Company::query()->select(['id', 'approved_at'])->get()->each(function (Company $company) use ($innerProgressBar): void {
            if (empty($company->approved_at)) {
                $company->updateQuietly(['approved_at' => now()]);
            }
            $innerProgressBar->advance();
        });
        $progressBar->advance();

        $innerProgressBar = $this->output->createProgressBar(Person::query()->count());
        Person::query()->select(['id', 'approved_at'])->get()->each(function (Person $person) use ($innerProgressBar): void {
            if (empty($person->approved_at)) {
                $person->updateQuietly(['approved_at' => now()]);
            }
            $innerProgressBar->advance();
        });
        $progressBar->advance();

        $innerProgressBar = $this->output->createProgressBar(Investor::query()->count());
        Investor::query()->select(['id', 'approved_at'])->get()->each(function (Investor $investor) use ($innerProgressBar): void {
            if (empty($investor->approved_at)) {
                $investor->updateQuietly(['approved_at' => now()]);
            }
            $innerProgressBar->advance();
        });
        $progressBar->advance();

        $innerProgressBar = $this->output->createProgressBar(Alternative::query()->count());
        Alternative::query()->select(['id', 'approved_at'])->get()->each(function (Alternative $alternative) use ($innerProgressBar): void {
            if (empty($alternative->approved_at)) {
                $alternative->updateQuietly(['approved_at' => now()]);
            }
            $innerProgressBar->advance();
        });
        $progressBar->finish();
    }
}
