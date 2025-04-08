<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadImportedICompaniesImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:download-companies-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // BackupTables::generateBackup([Company::class, Image::class]);

        storage_path('app/public/images/companies/');
        $companiesOptimizedImagePath = storage_path('app/public/images/companies/optimized/');

        File::makeDirectory(
            $companiesOptimizedImagePath,
            0755,
            true,
            true
        );

        $progressBar = $this->output->createProgressBar(
            Company::query()->has('logo')->count()
        );

        // $companyImagePath = get_image_archive_path(data_get($data, 'logo'), 'companies');
        //
        // if (!add_image_for_model($companyImagePath, $company)) {
        //    dump("Failed to add image to model ".get_class($company).":".$company->id);
        //
        //    //if (Str::isUrl(data_get($data, 'logo'))) {
        //    //    dump("\n Try to Download it from Url..");
        //    //    $company->addMediaFromUrl(data_get($data, 'logo'));
        //    //}
        // }

        Company::query()
            ->withWhereHas('logo', function ($query): void {
                $query->whereNull('type')
                    ->whereRaw("path IS NOT NULL and path != ''");
            })
            ->chunk(30, function ($companies) use ($progressBar): void {
                $companies->each(callback: function ($company) use ($progressBar): void {
                    /**
                     * if file exists in the folders, don't download it again
                     */
                    if (File::exists(
                        Storage::path(
                            'images/companies/' . basename(parse_url($company->logo->path, PHP_URL_PATH))
                        )
                    )) {
                        $this->info("\nSkipped " . $company->name);

                        return;
                    }

                    if (! Str::isUrl($company->logo->path)) {
                        $this->info("\nSkipped " . $company->name . ' because non valid url');
                        $company->logo()->update(['type' => 'migrated']);

                        return;
                    }

                    $company
                        ->addMediaFromUrl($company->logo->path)
                        ->toMediaCollection();

                    // after we download the image and move it from avatar column to media library
                    // then we don't need it anymore. This will give us a flag for moved images
                    $company->logo()->update(['type' => 'migrated']);

                    $this->info("\ndownloaded logo for " . $company->name);

                    $progressBar->advance();
                });
            });

        $progressBar->finish();

    }
}
