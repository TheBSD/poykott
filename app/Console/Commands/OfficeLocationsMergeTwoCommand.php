<?php

namespace App\Console\Commands;

use App\Actions\OfficeLocationsMergerAction;
use App\Models\OfficeLocation;
use Illuminate\Console\Command;

class OfficeLocationsMergeTwoCommand extends Command
{
    protected $signature = 'office:merge-two {from} {to}';

    protected $description = 'Merge office locations into one';

    /**
     * Execute the console command.
     */
    public function handle(
        OfficeLocationsMergerAction $officeLocationsMergerAction,
    ): void {
        $from = OfficeLocation::whereName($this->argument('from'))->firstOrFail();
        $to = OfficeLocation::whereName($this->argument('to'))->firstOrFail();

        $officeLocationsMergerAction->execute($from, $to);
    }
}
