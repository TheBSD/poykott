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
    public function handle(): void
    {

        $progressBar = $this->output->createProgressBar(9);

        Artisan::call(ImportTeamTechAvivCommand::class); // done
        $progressBar->advance();

        Artisan::call(ImportUnicornTechAvivCommand::class); // done
        $progressBar->advance();

        Artisan::call(ImportUnicornGraduatesTechAvivCommand::class); // done
        $progressBar->advance();

        Artisan::call(ImportPortfolioTechAvivCommand::class); // done
        $progressBar->advance();

        Artisan::call(ImportMembersTechAvivCommand::class); // done
        $progressBar->advance();

        Artisan::call(ImportJobCompaniesTechAvivCommand::class); // done
        $progressBar->advance();

        Artisan::call(ImportJobsTechAvivCommand::class);  // done
        $progressBar->advance();

        Artisan::call(ImportCompaniesOldSiteCommand::class);
        $progressBar->advance();

        //Artisan::call(AttachCompaniesImagesCommand::class);
        //$progressBar->advance();

        //Artisan::call(AttachPeopleImagesCommand::class);
        //$progressBar->advance();

        //Artisan::call(CleanNotesFromImagesUrlCommand::class);
        //$progressBar->advance();

        $progressBar->finish();
    }
}
