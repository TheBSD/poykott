<?php

namespace App\Console\Commands\Media;

use App\Models\Alternative;
use App\Models\Company;
use App\Models\Investor;
use App\Models\Person;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseMigrationCommand extends Command
{
    /**
     * Retrieve the records to be processed.
     */
    protected function getRecordsToMigrate(?int $limit): Collection
    {
        $query = $this->getModel()->newQuery();
        if ($limit !== null && $limit > 0) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Migrate a model's media files to S3.
     */
    protected function migrateModelMedia(mixed $model): void
    {
        try {
            $this->info("Processing model ID: {$model->id}");

            /** @var Alternative|Company|Investor|Person $model */
            $model->moveModelFilesToS3();
            $this->info("Successfully migrated media for model ID: {$model->id}");
        } catch (Exception $e) {
            $this->error("Failed to migrate media for model ID: {$model->id}. Error: {$e->getMessage()}");
        }
    }

    /**
     * Get the limit argument from the console command.
     */
    protected function getLimit(): ?int
    {
        return $this->option('limit') ? (int) $this->option('limit') : null;
    }

    /**
     * Get the model associated with the migration.
     *
     * @return Model
     */
    abstract protected function getModel();

    /**
     * Handle the migration process.
     */
    public function handle(): void
    {
        $limit = $this->getLimit();
        $this->info('Starting media migration to S3 ...');
        try {
            $recordsToMigrate = $this->getRecordsToMigrate($limit);
            if ($recordsToMigrate->isEmpty()) {
                $this->warn('No records found to migrate.');

                return;
            }
            foreach ($recordsToMigrate as $model) {
                $this->migrateModelMedia($model);
            }
            $this->info('Media migration completed successfully.');
        } catch (Exception $e) {
            $this->error("An error occurred: {$e->getMessage()}");
        }
    }
}
