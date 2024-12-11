<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Person;
use Illuminate\Console\Command;

class CleanNotesFromImagesUrlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean-notes-from-images-url';

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
        $progressBar = $this->output->createProgressBar(
            $count = Person::query()->whereNotNull('notes')->count()
                + Company::query()->whereNotNull('notes')->count()
        );

        Person::lazy()->each(function ($person) use ($progressBar) {
            if ($person->notes?->first() === 'image attached') {
                $person->update(['notes' => null]);

                $progressBar->advance();
            }
        });

        Company::lazy()->each(function ($person) use ($progressBar) {
            if ($person->notes?->first() === 'image attached') {
                $person->update(['notes' => null]);

                $progressBar->advance();
            }
        });

        $progressBar->finish();
    }
}
