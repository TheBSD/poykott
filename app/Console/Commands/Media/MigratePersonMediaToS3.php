<?php

namespace App\Console\Commands\Media;

use App\Models\Person;

class MigratePersonMediaToS3 extends BaseMigrationCommand
{
    protected $signature = 'migrate:persons-media-to-s3
                            {--limit= : Limit the number of companies to process in this run}';

    protected $description = 'Migrate media files of companies to S3';

    /**
     * Get the model to be used for migration.
     */
    protected function getModel(): Person
    {
        return new Person;
    }
}
