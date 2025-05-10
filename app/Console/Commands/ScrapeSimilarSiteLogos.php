<?php

namespace App\Console\Commands;

use App\Actions\ScrapeLogoFromUrlAction;
use App\Models\SimilarSite;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class ScrapeSimilarSiteLogos extends Command
{
    protected $signature = 'similar-site:scrape-logos';

    protected $description = 'Scrape and store website images for all SimilarSites';

    /**
     * @throws Throwable
     */
    public function handle(ScrapeLogoFromUrlAction $fetchImage): int
    {
        $sites = SimilarSite::query()->doesntHave('media')->get();

        if ($sites->isEmpty()) {
            $this->info('✅ All SimilarSite records already have images attached.');

            return CommandAlias::SUCCESS;
        }

        $this->info("🔍 Processing {$sites->count()} SimilarSite records...");

        $successCount = 0;
        $failCount = 0;

        foreach ($sites as $site) {
            $this->line("➡️ {$site->url}");
            $success = $fetchImage->execute($site, $site->url);

            if ($success) {
                $this->info('   ✅ Image saved.');
                $successCount++;
            } else {
                $this->warn('   ⚠️ Failed to fetch image.');
                $failCount++;
            }
        }

        $this->newLine();
        $this->info("🎉 Done. {$successCount} succeeded, {$failCount} failed.");

        return CommandAlias::SUCCESS;
    }
}
