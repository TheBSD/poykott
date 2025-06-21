<?php

namespace App\Console\Commands;

use App\Actions\OfficeLocationsMergerAction;
use App\Models\OfficeLocation;
use Illuminate\Console\Command;
use Throwable;

class OfficeLocationsMergerAllCommand extends Command
{
    public const SIMILARITY_THRESHOLD = 90;

    protected $signature = 'office:merge-all-similars';

    protected $description = 'Merge all similar office locations into one distinct office location';

    /**
     * Execute the console command.
     *
     * @throws Throwable
     */
    public function handle(
        OfficeLocationsMergerAction $officeLocationsMergerAction,
    ): void {

        $officeLocations = OfficeLocation::query()
            ->withCount('companies')
            ->with('companies:id,name')
            ->orderBy('companies_count', 'asc')
            ->get();

        $progressBar = $this->output->createProgressBar($officeLocations->count());
        $progressBar->start();

        // Group locations by name first to avoid nested loop issues
        $locationsByName = $officeLocations->groupBy('name');

        foreach ($locationsByName as $locations) {
            // Filter out non-existing locations
            $existingLocations = $locations->filter(function ($location) {
                return $location->exists;
            });

            // Skip if we don't have multiple locations with the same name
            if ($existingLocations->count() <= 1) {
                continue;
            }

            // Use the location with the highest companies_count as the target
            $targetLocation = $existingLocations->sortByDesc('companies_count')->first();

            // Merge all other locations into the target
            foreach ($existingLocations as $location) {
                if ($location->id !== $targetLocation->id) {
                    $officeLocationsMergerAction->execute($location, $targetLocation);
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
    }
}
