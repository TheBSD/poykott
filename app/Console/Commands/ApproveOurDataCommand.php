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
    public function handle()
    {
        $progressBar = $this->output->createProgressBar(4);

        $innerProgressBar = $this->output->createProgressBar(Company::count());
        Company::select(['id', 'approved_at'])->get()->each(function (Company $company) use ($innerProgressBar) {
            if (empty($company->approved_at)) {
                $company->updateQuietly(['approved_at' => now()]);
            }
            $innerProgressBar->advance();
        });
        $progressBar->advance();

        $innerProgressBar = $this->output->createProgressBar(Person::count());
        Person::select(['id', 'approved_at'])->get()->each(function (Person $person) use ($innerProgressBar) {
            if (empty($person->approved_at)) {
                $person->updateQuietly(['approved_at' => now()]);
            }
            $innerProgressBar->advance();
        });
        $progressBar->advance();

        $innerProgressBar = $this->output->createProgressBar(Investor::count());
        Investor::select(['id', 'approved_at'])->get()->each(function (Investor $investor) use ($innerProgressBar) {
            if (empty($investor->approved_at)) {
                $investor->updateQuietly(['approved_at' => now()]);
            }
            $innerProgressBar->advance();
        });
        $progressBar->advance();

        $innerProgressBar = $this->output->createProgressBar(Alternative::count());
        Alternative::select(['id', 'approved_at'])->get()->each(function (Alternative $alternative) use ($innerProgressBar) {
            if (empty($alternative->approved_at)) {
                $alternative->updateQuietly(['approved_at' => now()]);
            }
            $innerProgressBar->advance();
        });
        $progressBar->finish();
    }
}
