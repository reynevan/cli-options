<?php

namespace Reynevan\CliOptions\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Reynevan\CliOptions\Help;
use Reynevan\CliOptions\Option;

class HelpTest extends TestCase
{
    public function testRenderShouldListUsageOptionsAndExample(): void
    {
        $help = new Help('php index.php [options]', [
            new Option('port', 'p', 5353, description: 'Port to listen on', valueName: 'port'),
            new Option('zone', null, 'example.zone', description: 'Zone file', valueName: 'file'),
            new Option('verbose', 'v', false, description: 'Verbose mode', isFlag: true),
            new Option('quiet', 'q', false, isFlag: true),
        ], 'php index.php -p 53');

        $expected = implode(PHP_EOL, [
            'Usage: php index.php [options]',
            'OPTIONS',
            '  -p/--port <port>: Port to listen on',
            '  --zone <file>: Zone file',
            '  -v/--verbose: Verbose mode',
            '  -q/--quiet',
            'EXAMPLE:',
            '  php index.php -p 53',
        ]) . PHP_EOL;

        $this->assertSame($expected, $help->render());
    }

    public function testRenderShouldOmitExampleSectionWhenNotGiven(): void
    {
        $help = new Help('app [options]', [
            new Option('name', 'n', 'x', description: 'Name'),
        ]);

        $this->assertSame(
            'Usage: app [options]' . PHP_EOL . 'OPTIONS' . PHP_EOL . '  -n/--name <value>: Name' . PHP_EOL,
            $help->render()
        );
    }

    public function testPrintShouldEchoRenderedHelp(): void
    {
        $help = new Help('app', [new Option('flag', 'f', false, isFlag: true)]);

        $this->expectOutputString($help->render());
        $help->print();
    }
}
