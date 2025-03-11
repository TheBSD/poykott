<?php

namespace App\Actions;

use App\Models\OfficeLocation;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\ConsoleOutput;

class OfficeLocationsMergerAction
{
    public function __construct(public ConsoleOutput $output) {}

    /**
     * Merge two office locations that are similar into one office location.
     */
    public function execute(OfficeLocation $from, OfficeLocation $to)
    {
        return DB::transaction(function () use ($from, $to): void {
            $this->mergeCompaniesLog($from, $to);
            $this->mergeCompanies($from, $to);

            $this->deleteFromLocationLog($from);
            $this->deleteFromLocation($from);
        });
    }

    private function deleteFromLocationLog(OfficeLocation $from): void
    {
        $this->info("Removing office location: '{$from->name}' with ID: '{$from->id}'");
    }

    /**
     * Merge companies log.
     */
    private function mergeCompaniesLog(OfficeLocation $from, OfficeLocation $to): void
    {
        $this->info("\nRelating office location from '{$from->name}' to '{$to->name}'");

        $fromCompaniesNames = $from->companies->pluck('name')->implode(', ');
        $toCompaniesNames = $to->companies->pluck('name')->implode(', ');

        $this->info("Companies: '{$fromCompaniesNames}' will be merged into '{$to->name}' office location, in addition to '{$toCompaniesNames}'");
    }

    /**
     * Merge the companies from the 'from' location into the 'to' location.
     */
    private function mergeCompanies(OfficeLocation $from, OfficeLocation $to): void
    {
        $to->companies()->syncWithoutDetaching($from->companies->pluck('id')->toArray());
    }

    /**
     * Delete the 'from' location after merging.
     */
    private function deleteFromLocation(OfficeLocation $from): void
    {
        $from->delete();
    }

    private function info(string $string): void
    {
        $this->line($string, 'info');
    }

    private function line($string, $style = null): void
    {
        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->output->writeln($styled);
    }
}
