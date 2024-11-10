<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ImportAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all tech Aviv data';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $progressBar = $this->output->createProgressBar(8);

        Artisan::call(ImportTeamTechAvivCommand::class);
        $progressBar->advance();

        Artisan::call(ImportUnicornTechAvivCommand::class);
        $progressBar->advance();

        Artisan::call(ImportUnicornGraduatesTechAvivCommand::class);
        $progressBar->advance();

        Artisan::call(ImportPortfolioTechAvivCommand::class);
        $progressBar->advance();

        Artisan::call(ImportMembersTechAvivCommand::class);
        $progressBar->advance();

        Artisan::call(ImportJobCompaniesTechAvivCommand::class);
        $progressBar->advance();

        Artisan::call(ImportJobsTechAvivCommand::class);
        $progressBar->advance();

        Artisan::call(ImportCompaniesOldSiteCommand::class);
        $progressBar->advance();

        $progressBar->finish();
    }
}
