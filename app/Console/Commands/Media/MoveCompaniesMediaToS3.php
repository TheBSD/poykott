<?php

namespace App\Console\Commands\Media;

use App\Models\Company;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class MoveCompaniesMediaToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:companies-media-to-s3
                            {--limit= : Limit the number of companies to process in this run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate media files of companies to S3';

    /**
     * Retrieve the companies to be processed.
     *
     * @return Collection
     */
    protected function getCompaniesToMigrate(?int $limit)
    {
        $query = Company::query();
        if ($limit !== null && $limit !== 0) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Migrate a company's media files to S3.
     *
     * @param  Company  $company
     */
    protected function migrateCompanyMedia($company): void
    {
        try {
            $this->info("Processing company ID: {$company->id}");
            $company->moveModelFilesToS3($company); // Using the trait method
            $this->info("Successfully migrated media for company ID: {$company->id}");
        } catch (Exception $e) {
            $this->error("Failed to migrate media for company ID: {$company->id}. Error: " . $e->getMessage());
        }
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $this->info('Starting media migration to S3...');

        try {
            $companies = $this->getCompaniesToMigrate($limit);
            if ($companies->isEmpty()) {
                $this->warn('No companies found to migrate.');

                return Command::SUCCESS;
            }
            foreach ($companies as $company) {
                $this->migrateCompanyMedia($company);
            }
            $this->info('Media migration completed successfully.');

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
