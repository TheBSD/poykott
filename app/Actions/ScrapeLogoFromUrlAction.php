<?php

namespace App\Actions;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DomCrawler\Crawler;

class ScrapeLogoFromUrlAction
{
    public function __construct(public ConsoleOutput $output) {}

    /**
     * Scrape the most likely to be the logo image from a url.
     *
     * @Note This method has success rate almost 50-60%
     *
     * @param  HasMedia  $model  The model to attach the image to. Should be implement HasMedia to can attach media to
     * @param  string  $url  The URL of the website to fetch the image from
     * @return bool True if the image was fetched, false if not.
     */
    public function execute(HasMedia $model, string $url): bool
    {
        $client = new Client;

        try {
            // Make an HTTP request to the website
            $response = $client->get($url);
            $html = (string) $response->getBody();

            // Use Symfony DomCrawler to extract image sources
            $crawler = new Crawler($html);

            // Get images <img src="...">
            $images = $crawler->filter('img')->each(function (Crawler $node) use ($model): ?string {
                $src = $node->attr('src');

                return $src !== null && $src !== '' && $src !== '0' ? $this->ensureFullUrl($src, $model->url) : null;
            });

            $images = collect($images)
                ->filter()
                ->unique()
                ->values();

            if ($images->isEmpty()) {
                $this->warn("   ⚠️ No images found for {$model->url}");

                return false;
            }

            $bestImageUrl = $this->chooseBestImage($images);

            if ($bestImageUrl === null || $bestImageUrl === '' || $bestImageUrl === '0') {
                $this->warn("   ⚠️ No suitable logo found for {$model->url}");

                return false;
            }

            $model->addMediaFromUrl($bestImageUrl)->toMediaCollection();

            Log::info("✅ Image saved for model ID {$model->id}");

            return true;
        } catch (Exception|GuzzleException $e) {
            $this->error("   ❌ Failed for {$model->url}: {$e->getMessage()}");
            Log::error("❌ Error fetching image for model ID {$model->id}: " . $e->getMessage());

            return false;
        }
    }

    private function ensureFullUrl(string $imageUrl, string $baseUrl): string
    {
        try {
            $base = new Uri($baseUrl);
            $relative = new Uri($imageUrl);

            return (string) UriResolver::resolve($base, $relative);
        } catch (Exception) {
            // Fallback: old method if resolution fails
            $parsed = parse_url($baseUrl);

            return $parsed['scheme'] . '://' . $parsed['host'] . '/' . ltrim($imageUrl, '/');
        }
    }

    private function chooseBestImage(iterable $images): ?string
    {
        $candidates = collect($images)->mapWithKeys(function ($url) {
            $score = 0;

            if (Str::contains(Str::lower($url), ['logo'])) {
                $score += 5;
            }

            if (Str::contains(Str::lower($url), ['logo', 'brand', 'icon'])) {
                $score += 3;
            }

            if (Str::endsWith($url, ['.svg', '.png', '.jpg', '.jpeg', '.webp'])) {
                $score += 1;
            }

            // Prefer larger size names like "500x200"
            if (preg_match('/\d{2,4}x\d{2,4}/', $url)) {
                $score += 2;
            }

            return [$url => $score];
        });

        return $candidates->sortDesc()->keys()->first();
    }

    private function line(string $string, $style = null): void
    {
        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->output->writeln($styled);
    }

    private function warn(string $string): void
    {
        $this->line($string, 'warning');
    }

    private function error(string $string): void
    {
        $this->line($string, 'error');
    }
}
