<?php

namespace App\Console\Commands;

use App\Actions\CalculateSimilarityTextsAction;
use App\Actions\OfficeLocationsMergerAction;
use App\Models\OfficeLocation;
use Illuminate\Console\Command;

class OfficeLocationsMergerAllCommand extends Command
{
    public const SIMILARITY_THRESHOLD = 90;

    protected $signature = 'office:merge-all-similars';

    protected $description = 'Merge all similar office locations into one distinct office location';

    /**
     * Execute the console command.
     */
    public function handle(
        OfficeLocationsMergerAction $officeLocationsMergerAction,
        CalculateSimilarityTextsAction $calculateSimilarityTextsAction
    ): void {

        $officeLocations = OfficeLocation::query()
            ->with('companies')
            ->get();

        $progressBar = $this->output->createProgressBar($officeLocations->count());
        $progressBar->start();

        foreach ($officeLocations as $from) {
            foreach ($officeLocations as $to) {
                // Skip comparing the same office location
                if ($from->id === $to->id) {
                    continue;
                }
                // Skip when one of the office locations doesn't exist
                if (! $from->exists) {
                    continue;
                }
                if (! $to->exists) {
                    continue;
                }

                $similarity = $calculateSimilarityTextsAction->execute($from->name, $to->name);

                if ($similarity >= self::SIMILARITY_THRESHOLD) {
                    $officeLocationsMergerAction->execute($from, $to);
                }
            }
            $progressBar->advance();
        }
        $progressBar->finish();
    }
}
