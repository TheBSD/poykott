<?php

namespace App\Console\Commands\Media;

use App\Models\Investor;

class MigrateInvestorMediaToS3 extends BaseMigrationCommand
{
    protected $signature = 'migrate:investors-media-to-s3
                            {--limit= : Limit the number of companies to process in this run}';

    protected $description = 'Migrate media files of investors to S3';

    /**
     * Get the model to be used for migration.
     */
    protected function getModel(): Investor
    {
        return new Investor;
    }
}
