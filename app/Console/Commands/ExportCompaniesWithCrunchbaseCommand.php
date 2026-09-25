<?php

namespace App\Console\Commands;

use App\Enums\ResourceType;
use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Filesystem\Path;

class ExportCompaniesWithCrunchbaseCommand extends Command
{
    protected $signature = 'companies:export-crunchbase {output? : Output JSON path (default: companies-with-crunchbase.json in the project root)}';

    protected $description = 'Export companies with a Crunchbase URL to JSON';

    public function handle(): int
    {
        $crunchbase = fn ($query) => $query
            ->where('type', ResourceType::Crunchbase->value)
            ->whereNotNull('url')
            ->where('url', '!=', '');

        $companies = Company::query()
            ->select(['id', 'name', 'slug', 'url'])
            ->whereHas('resources', $crunchbase)
            ->with(['resources' => $crunchbase])
            ->get()
            ->map(fn (Company $company): array => [
                'id' => $company->id,
                'name' => $company->name,
                'slug' => $company->slug,
                'url' => $company->url,
                'crunchbase_url' => $company->resources->first()->url,
            ]);

        $path = $this->prepareOutputPath();

        if (! $this->writeExport($path, $companies)) {
            $this->error('Could not write the export file.');

            return self::FAILURE;
        }

        $this->info('Exported ' . $companies->count() . ' companies to ' . $path);

        return self::SUCCESS;
    }

    private function prepareOutputPath(): string
    {
        $path = Path::makeAbsolute($this->argument('output') ?: 'companies-with-crunchbase.json', base_path());
        File::ensureDirectoryExists(dirname($path));

        return $path;
    }

    private function writeExport(string $path, Collection $companies): bool
    {
        return File::put($path, $companies->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) !== false;
    }
}
