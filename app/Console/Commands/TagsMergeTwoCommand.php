<?php

namespace App\Console\Commands;

use App\Actions\TagsMergerAction;
use App\Models\Tag;
use Illuminate\Console\Command;
use Throwable;

class TagsMergeTwoCommand extends Command
{
    protected $signature = 'tag:merge-two {from} {to}';

    protected $description = 'Merge two tags into one';

    /**
     * Execute the console command.
     *
     * @throws Throwable
     */
    public function handle(TagsMergerAction $tagsMergerAction): void
    {
        $from = Tag::whereName($this->argument('from'))->firstOrFail();
        $to = Tag::whereName($this->argument('to'))->firstOrFail();

        $tagsMergerAction->execute($from, $to);
    }
}
