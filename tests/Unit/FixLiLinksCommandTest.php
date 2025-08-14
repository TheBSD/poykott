<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Console\Commands\FixLiLinksCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JsonException;
use JsonMachine\Items;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Testing library and framework: PHPUnit (Laravel-style unit tests).
 * We focus on pure/protected helpers to avoid heavy I/O, mocking facades where needed.
 */
#[CoversClass(FixLiLinksCommand::class)]
class FixLiLinksCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeCommand(): FixLiLinksCommand
    {
        return new class extends FixLiLinksCommand {
            // Expose protected methods for direct testing via wrapper methods.
            public function callNormalizeUrl(string $url): string
            {
                return $this->normalizeUrl($url);
            }
            public function callHasLiNodes(mixed $value): bool
            {
                return $this->hasLiNodes($value);
            }
            public function callFixLinkInItem(array $item): array
            {
                return $this->fixLinkInItem($item);
            }
            public function callIsValidJson(string $file): bool
            {
                return $this->isValidJson($file);
            }
            public function callTransformItems(iterable $items, $progressBar): \Generator
            {
                return $this->transformItems($items, $progressBar);
            }
            public function callValidateInputs(string $input, string $output): bool
            {
                return $this->validateInputs($input, $output);
            }
        };
    }

    public function test_normalize_url_adds_https_and_trailing_slash_and_trims_whitespace(): void
    {
        $cmd = $this->makeCommand();

        $this->assertSame('https://example.com/', $cmd->callNormalizeUrl('example.com'));
        $this->assertSame('https://example.com/', $cmd->callNormalizeUrl('example.com/'));
        $this->assertSame('https://example.com/', $cmd->callNormalizeUrl('  example.com  '));
        $this->assertSame('https://example.com/', $cmd->callNormalizeUrl("\n\texample.com\t\n"));
    }

    public function test_normalize_url_preserves_existing_protocol_and_adds_trailing_slash(): void
    {
        $cmd = $this->makeCommand();

        $this->assertSame('http://example.com/', $cmd->callNormalizeUrl('http://example.com'));
        $this->assertSame('https://example.com/', $cmd->callNormalizeUrl('https://example.com'));
        $this->assertSame('https://example.com/', $cmd->callNormalizeUrl('https://example.com/'));
        $this->assertSame('http://example.com/path/', $cmd->callNormalizeUrl('http://example.com/path'));
        $this->assertSame('https://example.com/path/', $cmd->callNormalizeUrl('https://example.com/path'));
    }

    public function test_has_li_nodes_detects_valid_li_array(): void
    {
        $cmd = $this->makeCommand();

        $this->assertTrue($cmd->callHasLiNodes(['li' => []]));
        $this->assertTrue($cmd->callHasLiNodes(['li' => [['link' => 'x']]]));
    }

    public function test_has_li_nodes_rejects_missing_or_invalid_li(): void
    {
        $cmd = $this->makeCommand();

        $this->assertFalse($cmd->callHasLiNodes(null));
        $this->assertFalse($cmd->callHasLiNodes('string'));
        $this->assertFalse($cmd->callHasLiNodes(['li' => null]));
        $this->assertFalse($cmd->callHasLiNodes(['li' => 'not-an-array']));
        $this->assertFalse($cmd->callHasLiNodes(['other' => []]));
    }

    public function test_fix_link_in_item_normalizes_when_link_is_string(): void
    {
        $cmd = $this->makeCommand();
        $item = ['link' => 'example.com'];

        $result = $cmd->callFixLinkInItem($item);

        $this->assertSame('https://example.com/', $result['link']);
        $this->assertArrayHasKey('link', $result);
    }

    public function test_fix_link_in_item_returns_item_unchanged_when_link_missing_or_not_string(): void
    {
        $cmd = $this->makeCommand();

        $this->assertSame(['name' => 'x'], $cmd->callFixLinkInItem(['name' => 'x']));
        $this->assertSame(['link' => ['nested' => true]], $cmd->callFixLinkInItem(['link' => ['nested' => true]]));
        $this->assertSame(['link' => 123], $cmd->callFixLinkInItem(['link' => 123]));
    }

    public function test_is_valid_json_returns_true_for_valid_json_sample(): void
    {
        // Mock File::get to return valid JSON (only first 1000 chars are parsed)
        $file = __DIR__ . '/fixtures/valid.json';
        $json = json_encode(['a' => 1, 'b' => ['c' => 2]], JSON_THROW_ON_ERROR);

        // Since we are not bootstrapping Laravel, simulate File facade via class alias if available.
        // If not, we fallback to creating the file temporarily.
        if (class_exists(File::class)) {
            Mockery::mock('alias:' . File::class)
                ->shouldReceive('get')
                ->with($file, true)
                ->andReturn($json);
        } else {
            // Fallback: write to file so native File::get is not needed.
            if (!is_dir(dirname($file))) {
                mkdir(dirname($file), 0777, true);
            }
            file_put_contents($file, $json);
        }

        $cmd = $this->makeCommand();
        $this->assertTrue($cmd->callIsValidJson($file));
    }

    public function test_is_valid_json_returns_false_for_invalid_json_sample(): void
    {
        $file = __DIR__ . '/fixtures/invalid.json';
        $invalid = '{"a": 1, "b": [3, }'; // broken JSON

        if (class_exists(File::class)) {
            Mockery::mock('alias:' . File::class)
                ->shouldReceive('get')
                ->with($file, true)
                ->andReturn($invalid);
        } else {
            if (!is_dir(dirname($file))) {
                mkdir(dirname($file), 0777, true);
            }
            file_put_contents($file, $invalid);
        }

        $cmd = $this->makeCommand();
        $this->assertFalse($cmd->callIsValidJson($file));
    }

    public function test_validate_inputs_fails_when_input_does_not_exist(): void
    {
        $cmd = $this->makeCommand();

        if (class_exists(File::class)) {
            $fileMock = Mockery::mock('alias:' . File::class);
            $fileMock->shouldReceive('exists')->with('/path/in.json')->andReturn(false);
        }

        $this->assertFalse($cmd->callValidateInputs('/path/in.json', '/out/out.json'));
    }

    public function test_validate_inputs_fails_when_input_not_readable(): void
    {
        $cmd = $this->makeCommand();

        if (class_exists(File::class)) {
            $fileMock = Mockery::mock('alias:' . File::class);
            $fileMock->shouldReceive('exists')->with('/path/in.json')->andReturn(true);
            $fileMock->shouldReceive('isReadable')->with('/path/in.json')->andReturn(false);
        }

        $this->assertFalse($cmd->callValidateInputs('/path/in.json', '/out/out.json'));
    }

    public function test_validate_inputs_fails_when_output_directory_missing(): void
    {
        $cmd = $this->makeCommand();

        if (class_exists(File::class)) {
            $fileMock = Mockery::mock('alias:' . File::class);
            $fileMock->shouldReceive('exists')->with('/path/in.json')->andReturn(true);
            $fileMock->shouldReceive('isReadable')->with('/path/in.json')->andReturn(true);
            $fileMock->shouldReceive('isDirectory')->with('/missing')->andReturn(false);
        }

        $this->assertFalse($cmd->callValidateInputs('/path/in.json', '/missing/out.json'));
    }

    public function test_validate_inputs_passes_with_validation_flag_and_valid_json(): void
    {
        // We can't easily toggle $this->option('validate') without the framework wiring.
        // Instead, we reflect and temporarily override the option() method for this instance.
        $cmd = new class extends FixLiLinksCommand {
            protected array $fakeOptions = ['validate' => true];
            public function setFakeOptions(array $opts): void { $this->fakeOptions = $opts; }
            public function option($key = null)
            {
                return $this->fakeOptions[$key] ?? null;
            }
            public function callValidateInputs(string $input, string $output): bool
            {
                return $this->validateInputs($input, $output);
            }
            public function callIsValidJson(string $file): bool
            {
                return $this->isValidJson($file);
            }
        };

        if (class_exists(File::class)) {
            $fileMock = Mockery::mock('alias:' . File::class);
            $fileMock->shouldReceive('exists')->with('/path/in.json')->andReturn(true);
            $fileMock->shouldReceive('isReadable')->with('/path/in.json')->andReturn(true);
            $fileMock->shouldReceive('isDirectory')->with('/out')->andReturn(true);
            // validate flag triggers isValidJson
            $fileMock->shouldReceive('get')->with('/path/in.json', true)->andReturn('{"ok":true}');
        }

        $this->assertTrue($cmd->callValidateInputs('/path/in.json', '/out/out.json'));
    }

    public function test_transform_items_advances_progress_and_fixes_links_in_li_arrays(): void
    {
        $cmd = $this->makeCommand();

        $items = [
            'k1' => ['li' => [['link' => 'example.com'], ['link' => 'https://already.com']]],
            'k2' => ['name' => 'no-li'],
            'k3' => ['li' => [['noLink' => true], ['link' => 'no-protocol.com/path']]],
        ];

        $progress = new class {
            public int $count = 0;
            public function advance(): void { $this->count++; }
        };

        $result = [];
        foreach ($cmd->callTransformItems($items, $progress) as $k => $v) {
            $result[$k] = $v;
        }

        // Progress should advance for each top-level item
        $this->assertSame(3, $progress->count);

        // k1: both links normalized with trailing slash
        $this->assertSame('https://example.com/', $result['k1']['li'][0]['link']);
        $this->assertSame('https://already.com/', $result['k1']['li'][1]['link']);

        // k2: unchanged
        $this->assertSame(['name' => 'no-li'], $result['k2']);

        // k3: first item unchanged (no 'link'), second normalized
        $this->assertSame(['noLink' => true], $result['k3']['li'][0]);
        $this->assertSame('https://no-protocol.com/path/', $result['k3']['li'][1]['link']);
    }

    public function test_transform_items_handles_empty_iterable(): void
    {
        $cmd = $this->makeCommand();
        $progress = new class {
            public int $count = 0;
            public function advance(): void { $this->count++; }
        };

        $items = [];
        $result = iterator_to_array($cmd->callTransformItems($items, $progress), true);

        $this->assertSame([], $result);
        $this->assertSame(0, $progress->count);
    }

    public function test_process_file_writes_streamed_json_structure_without_processing_when_no_items(): void
    {
        // This test focuses on I/O boundaries minimally by mocking File and Items iteration.
        // We assert that the stream is written with braces and no inner content when no items are yielded.

        $cmd = $this->makeCommand();

        // Prepare a real temporary output file path to observe what processFile writes.
        $output = sys_get_temp_dir() . '/fix_li_links_test_output_' . uniqid() . '.json';
        $input = '/path/in.json';

        // Mock File facade for put and for Items::fromFile call side effects.
        if (class_exists(File::class)) {
            $fileMock = Mockery::mock('alias:' . File::class);
            // processFile calls File::put($output, '', LOCK_EX);
            $fileMock->shouldReceive('put')->with($output, '', LOCK_EX)->andReturnTrue();
        }

        // Mock Items::fromFile($input) to return an empty iterator
        if (class_exists(Items::class)) {
            Mockery::mock('overload:' . Items::class)
                ->shouldReceive('fromFile')
                ->with($input)
                ->andReturn(new \ArrayIterator([]));
        }

        // Create a simple progress bar stub
        $progress = new class {
            public int $count = 0;
            public function advance(): void { $this->count++; }
        };

        // Invoke processFile via reflection since it's protected
        $ref = new ReflectionClass(FixLiLinksCommand::class);
        $method = $ref->getMethod('processFile');
        $method->setAccessible(true);

        // Execute
        $method->invoke($cmd, $input, $output, $progress);

        // Validate the output file now exists and contains empty JSON object formatting
        $this->assertFileExists($output);
        $content = file_get_contents($output);
        $this->assertSame("{\n\n}\n", $content);

        // Cleanup
        @unlink($output);
    }
}