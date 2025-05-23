<?php

namespace App\Console\Commands\Media;

use App\Models\Company;

class MigrateCompaniesMediaToS3 extends BaseMigrationCommand
{
    protected $signature = 'migrate:companies-media-to-s3
                            {--limit= : Limit the number of companies to process in this run}';

    protected $description = 'Migrate media files of companies to S3';

    /**
     * Get the model to be used for migration.
     */
    protected function getModel(): Company
    {
        return new Company;
    }
}
