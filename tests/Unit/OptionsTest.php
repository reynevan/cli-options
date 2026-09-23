<?php

namespace Reynevan\CliOptions\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Reynevan\CliOptions\InvalidOptionException;
use Reynevan\CliOptions\Option;
use Reynevan\CliOptions\Options;

class OptionsTest extends TestCase
{
    public function testParseShouldParseCorrectValues(): void
    {
        $options = Options::parse([
            'index.php',
            '-hp',
            '53',
            '--address',
            '127.0.0.1',
            '-v'
        ], $this->getOptionsDefinition());
        $this->assertSame($options->get('port'), 53);
        $this->assertSame($options->get('address'), '127.0.0.1');
        $this->assertSame($options->get('verbose'), true);
    }
    public function testParseShouldParseShortOptionWithValue(): void
    {
        $options = Options::parse([
            'index.php',
            '-p53',
        ], $this->getOptionsDefinition());
        $this->assertSame($options->get('port'), 53);
    }

    public function testParseShouldParseLongSyntaxWithEqualSign(): void
    {
        $options = Options::parse([
            'index.php',
            '--port=53',
        ], $this->getOptionsDefinition());
        $this->assertSame($options->get('port'), 53);
    }

    public function testParseShouldParseFlagAsNotLastOption(): void
    {
        $options = Options::parse([
            'index.php',
            '-v',
            '-p',
            '53'
        ], $this->getOptionsDefinition());
        $this->assertSame($options->get('verbose'), true);
        $this->assertSame($options->get('port'), 53);
    }

    public function testParseShouldCollectPositionalArgs(): void
    {
        $options = Options::parse([
            'index.php',
            '-p',
            '1',
            '-h',
            'arg1',
            'arg2'
        ], $this->getOptionsDefinition());
        $this->assertSame($options->get('port'), 1);
        $this->assertSame($options->get('help'), true);
        $this->assertSame($options->getArgs(), ['arg1', 'arg2']);
        $this->assertSame($options->getArg(0), 'arg1');
        $this->assertSame($options->getArg(2), null);
    }

    public function testParseShouldCollectPositionalArgsBeforeAndBetweenOptions(): void
    {
        $options = Options::parse([
            'index.php',
            'first',
            '--port=53',
            'second',
            '-v',
            'third'
        ], $this->getOptionsDefinition());
        $this->assertSame($options->get('port'), 53);
        $this->assertSame($options->get('verbose'), true);
        $this->assertSame($options->getArgs(), ['first', 'second', 'third']);
    }

    public function testParseShouldReturnEmptyArgsWhenNoneGiven(): void
    {
        $options = Options::parse([
            'index.php',
            '-v'
        ], $this->getOptionsDefinition());
        $this->assertSame($options->getArgs(), []);
    }

    public function testParseShouldTreatEverythingAfterDoubleDashAsArgs(): void
    {
        $options = Options::parse([
            'index.php',
            '-v',
            '--',
            '--port=53',
            '-h',
            'arg1'
        ], $this->getOptionsDefinition());
        $this->assertSame($options->get('verbose'), true);
        $this->assertSame($options->get('port'), 5353);
        $this->assertSame($options->get('help'), false);
        $this->assertSame($options->getArgs(), ['--port=53', '-h', 'arg1']);
    }

    public function testParseShouldTreatLoneDashAsArg(): void
    {
        $options = Options::parse([
            'index.php',
            '-',
        ], $this->getOptionsDefinition());
        $this->assertSame($options->getArgs(), ['-']);
    }

    public function testOptionValueShouldNotBecomeArg(): void
    {
        $options = Options::parse([
            'index.php',
            '-a',
            '127.0.0.1',
        ], $this->getOptionsDefinition());
        $this->assertSame($options->get('address'), '127.0.0.1');
        $this->assertSame($options->getArgs(), []);
    }

    public function testInvalidOptionShouldThrowException(): void
    {
        $this->expectException(InvalidOptionException::class);
        Options::parse([
            'index.php',
            '-p',
            '53',
            '--invalid-option',
            '127.0.0.1',
            '-v'
        ], $this->getOptionsDefinition());
    }

    /**
     * @return Option[]
     */
    private function getOptionsDefinition(): array
    {
        return[
            new Option('port', 'p', 5353, description: 'Port to listen to'),
            new Option('address', 'a', '0.0.0.0', description: 'Bind address'),
            new Option('zone', 'z', 'example.zone', description: 'Zone file'),
            new Option('verbose', 'v', false, description: 'Verbose mode', isFlag: true),
            new Option('help', 'h', false, description: 'Show this help', isFlag: true),
        ];
    }
}
