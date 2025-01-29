<?php

namespace App\Console\Commands\Media;

use App\Models\Alternative;

class MigrateAlternativeMediaToS3 extends BaseMigrationCommand
{
    protected $signature = 'migrate:alternatives-media-to-s3
                            {--limit= : Limit the number of alternatives to process in this run}';

    protected $description = 'Migrate media files of alternatives to S3';

    /**
     * Get the model to be used for migration.
     */
    protected function getModel(): Alternative
    {
        return new Alternative;
    }
}
