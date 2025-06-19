<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateCleanedOfficeLocationCommand extends Command
{
    protected $signature = 'office-locations:update-cleaned';

    protected $description = 'Updated cleaned office locations names after clean them with AI';

    public function handle(): ?int
    {
        $files = Storage::disk('local')->files('exports');
        natsort($files); // sort files naturally because the result is: file1, file10, file11, ... file2

        if ($files === []) {
            $this->warn('No JSON files found in: exports');

            return Command::SUCCESS;
        }

        $files = array_values($files); // reset ordered ids

        foreach ($files as $file) {
            $this->info("Processing: $file");

            $data = json_decode((string) Storage::disk('local')->get($file), true);

            $progressBar = $this->output->createProgressBar(count($data));

            foreach ($data as $row) {

                // skip rows without id
                if (! isset($row['id'])) {
                    continue;
                }

                $id = $row['id'];

                // Remove the key from update data
                $updatedData = array_filter($row, fn ($col): bool => $col !== 'id', ARRAY_FILTER_USE_KEY);

                DB::table('office_locations')->where('id', $id)->update($updatedData);

                $progressBar->advance();
            }

            $progressBar->finish();

            $this->info('Update complete.');

            return Command::SUCCESS;
        }

        return null;
    }
}
