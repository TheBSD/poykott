<?php

namespace App\Console\Commands;

use Exception;
use Generator;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JsonException;
use JsonMachine\Items;
use RuntimeException;

class FixLiLinksCommand extends Command
{
    protected $signature = 'fix:li-links
                            {input : Path to input JSON file}
                            {output : Path to output JSON file}
                            {--buffer-size=8192 : Buffer size for file operations}
                            {--validate : Validate input file before processing}';

    protected $description = 'Stream a large JSON file and fix links inside li nodes';

    protected function validateInputs(string $input, string $output): bool
    {
        if (! File::exists($input)) {
            $this->error("Input file does not exist: {$input}");

            return false;
        }

        if (! File::isReadable($input)) {
            $this->error("Input file is not readable: {$input}");

            return false;
        }

        $outputDir = dirname($output);
        if (! File::isDirectory($outputDir)) {
            $this->error("Output directory does not exist: {$outputDir}");

            return false;
        }

        if ($this->option('validate')) {
            $this->info('Validating JSON structure...');
            if (! $this->isValidJson($input)) {
                $this->error('Invalid JSON file structure');

                return false;
            }
        }

        return true;
    }

    protected function isValidJson(string $file): bool
    {
        try {
            $sample = File::get($file, true);
            json_decode(Str::limit($sample, 1000), true, 512, JSON_THROW_ON_ERROR);

            return true;
        } catch (JsonException) {
            return false;
        }
    }

    protected function processFile(string $input, string $output, $progressBar): void
    {
        $bufferSize = (int) $this->option('buffer-size');

        File::put($output, '', LOCK_EX);
        $stream = fopen($output, 'w');

        throw_unless($stream, new RuntimeException("Cannot open output file: {$output}"));

        stream_set_write_buffer($stream, $bufferSize);

        fwrite($stream, "{\n");

        $items = Items::fromFile($input);
        $isFirst = true;

        foreach ($this->transformItems($items, $progressBar) as $key => $value) {
            if (! $isFirst) {
                fwrite($stream, ",\n");
            }

            $encodedKey = json_encode($key, JSON_THROW_ON_ERROR);
            $encodedValue = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

            fwrite($stream, "{$encodedKey}: {$encodedValue}");
            $isFirst = false;
        }

        fwrite($stream, "\n}\n");
        fclose($stream);
    }

    protected function transformItems(iterable $items, $progressBar): Generator
    {
        foreach ($items as $key => $value) {
            $progressBar->advance();

            if ($this->hasLiNodes($value)) {
                $value['li'] = Collection::make($value['li'])
                    ->map(fn ($item): array => $this->fixLinkInItem($item))
                    ->all();
            }

            yield $key => $value;
        }
    }

    protected function hasLiNodes($value): bool
    {
        return is_array($value)
            && array_key_exists('li', $value)
            && is_array($value['li']);
    }

    protected function fixLinkInItem(array $item): array
    {
        if (! isset($item['link']) || ! is_string($item['link'])) {
            return $item;
        }

        $link = $this->normalizeUrl($item['link']);
        $item['link'] = $link;

        return $item;
    }

    protected function normalizeUrl(string $url): string
    {
        // Remove any leading/trailing whitespace
        $url = trim($url);

        $url = Str::of($url)->trim();

        // Add https:// if no protocol is present
        if (! Str::startsWith($url, ['http://', 'https://'])) {
            $url = Str::start($url, 'https://');
        }

        // Add trailing slash if not present
        $url = Str::finish($url, '/');

        return $url;
    }

    public function handle(): int
    {
        $input = $this->argument('input');
        $output = $this->argument('output');

        if (! $this->validateInputs($input, $output)) {
            return Command::FAILURE;
        }

        $this->info("Processing {$input}...");
        $bar = $this->output->createProgressBar();

        try {
            // $this->processFile($input, $output, $bar);
            $bar->finish();
            $this->newLine();
            $fullpath = realpath($output);
            $this->info("✅ Output saved to {$fullpath}");

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");

            return Command::FAILURE;
        }
    }
}
