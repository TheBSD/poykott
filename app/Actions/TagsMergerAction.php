<?php

namespace App\Actions;

use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\ConsoleOutput;
use Throwable;

class TagsMergerAction
{
    public function __construct(public ConsoleOutput $output) {}

    /**
     * Merge two tags that are similar into one tag.
     *
     * @throws Throwable
     */
    public function execute(Tag $from, Tag $to)
    {
        return DB::transaction(function () use ($from, $to): void {
            $this->mergeCompaniesLog($from, $to);
            $this->mergeTags($from, $to);

            $this->deleteFromTagLog($from);
            $this->deleteFromTag($from);
        });
    }

    private function deleteFromTagLog(Tag $from): void
    {
        $this->info("Removing tag: '{$from->name}' with ID: '{$from->id}'");
    }

    /**
     * Merge tags log.
     */
    private function mergeCompaniesLog(Tag $from, Tag $to): void
    {
        $this->info("\nRelating tag from '{$from->name}' to '{$to->name}'");

        $fromCompaniesNames = $from->companies->pluck('name')->implode(', ');
        $toCompaniesNames = $to->companies->pluck('name')->implode(', ');

        $fromAlternativesNames = $from->alternatives->pluck('name')->implode(', ');
        $toAlternativesNames = $to->alternatives->pluck('name')->implode(', ');

        $fromPeopleNames = $from->people->pluck('name')->implode(', ');
        $toPeopleNames = $to->people->pluck('name')->implode(', ');

        $fromInvestorsNames = $from->investors->pluck('name')->implode(', ');
        $toInvestorsNames = $to->investors->pluck('name')->implode(', ');

        if (filled($fromCompaniesNames) || filled($toCompaniesNames)) {
            $this->info(
                "Companies: '{$fromCompaniesNames}' will be merged into '{$to->name}' tag, in addition to '{$toCompaniesNames}'"
            );
        }

        if (filled($fromAlternativesNames) || filled($toAlternativesNames)) {
            $this->info(
                "Alternatives: '{$fromAlternativesNames}' will be merged into '{$to->name}' tag, in addition to '{$toAlternativesNames}'"
            );
        }

        if (filled($fromPeopleNames) || filled($toPeopleNames)) {
            $this->info(
                "People: '{$fromPeopleNames}' will be merged into '{$to->name}' tag, in addition to '{$toPeopleNames}'"
            );
        }

        if (filled($fromInvestorsNames) || filled($toInvestorsNames)) {
            $this->info(
                "Investors: '{$fromInvestorsNames}' will be merged into '{$to->name}' tag, in addition to '{$toInvestorsNames}'"
            );
        }
    }

    /**
     * Merge the tags from the 'from' tag into the 'to' tag.
     */
    private function mergeTags(Tag $from, Tag $to): void
    {
        $to->companies()->syncWithoutDetaching($from->companies->pluck('id')->toArray());
        $to->alternatives()->syncWithoutDetaching($from->alternatives->pluck('id')->toArray());
        $to->people()->syncWithoutDetaching($from->people->pluck('id')->toArray());
        $to->investors()->syncWithoutDetaching($from->investors->pluck('id')->toArray());
    }

    /**
     * Delete the 'from' tag after merging.
     */
    private function deleteFromTag(Tag $from): void
    {
        $from->delete();
    }

    private function info(string $string): void
    {
        $this->line($string, 'info');
    }

    private function line(string $string, $style = null): void
    {
        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->output->writeln($styled);
    }
}
