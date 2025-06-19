<?php

namespace App\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeocodeLocationAction
{
    protected string $baseUrl = 'https://nominatim.openstreetmap.org/search';

    public function __construct(
        protected int $cacheTtl = 2592000 // 30 days in seconds
    ) {}

    public function execute(string $location): ?array
    {
        $cacheKey = 'geocode_' . md5($location);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($location): ?array {
            try {
                sleep(1); // 1 second delay for rate limit

                $response = Http::withHeaders([
                    'User-Agent' => config('app.name', 'Laravel') . ' Application',
                ])->get($this->baseUrl, [
                    'q' => $location,
                    'format' => 'json',
                    'limit' => 1,
                ]);

                $data = $response->json()[0] ?? null;

                if ($response->successful() && $data) {
                    return [
                        'lat' => (float) $data['lat'],
                        'lng' => (float) $data['lon'],
                        'display_name' => $data['display_name'] ?? null,
                        'type' => $data['type'] ?? null,
                        'importance' => $data['importance'] ?? null,
                    ];
                }

                Log::warning('Geocoding failed', [
                    'location' => $location,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
            } catch (Throwable $e) {
                Log::error('Geocoding error', [
                    'location' => $location,
                    'exception' => $e->getMessage(),
                ]);
            }

            return null;
        });
    }
}
