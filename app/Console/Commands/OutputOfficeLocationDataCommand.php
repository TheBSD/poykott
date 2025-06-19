<?php

namespace App\Console\Commands;

use App\Models\OfficeLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OutputOfficeLocationDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'office-locations:output-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Output office location data to be batch processed';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Outputting office location data...');

        $counter = 1;
        OfficeLocation::query()
            ->select(['id', 'name'])
            ->chunk(500, function ($locations) use (&$counter): void {
                $data = $locations->toArray();

                $fileName = "exports/office_locations_{$counter}.json";

                Storage::disk('local')->put($fileName, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                $counter++;
            });

        $this->info('Office location data output complete.');

        $this->newLine();

    }
}
