<?php

namespace App\Console\Commands;

use App\Actions\CalculateSimilarityTextsAction;
use App\Actions\TagsMergerAction;
use App\Models\Tag;
use Illuminate\Console\Command;
use Throwable;

class TagsMergerAllCommand extends Command
{
    public const SIMILARITY_THRESHOLD = 90;

    protected $signature = 'tag:merge-all-similars';

    protected $description = 'Merge all similar tags into one distinct office location';

    /**
     * Execute the console command.
     *
     * @throws Throwable
     */
    public function handle(
        TagsMergerAction $tagsMergerAction,
        CalculateSimilarityTextsAction $calculateSimilarityTextsAction
    ): void {

        $tags = Tag::query()
            ->with(['alternatives', 'companies', 'investors', 'people'])
            ->get();

        $progressBar = $this->output->createProgressBar($tags->count());
        $progressBar->start();

        foreach ($tags as $from) {
            foreach ($tags as $to) {
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
                    $tagsMergerAction->execute($from, $to);
                }
            }
            $progressBar->advance();
        }
        $progressBar->finish();
    }
}
