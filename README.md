# reynevan/cli-options

Tiny, dependency-free command-line options parser for PHP 8.4+.

It does one thing: turns `$argv` into a set of typed values based on a list of
option definitions plus any leftover positional arguments, and can print a plain
usage/help listing for them. No commands, no sub-commands — just options.

## Installation

```bash
composer require reynevan/cli-options
```

## Usage

```php
<?php

use Reynevan\CliOptions\InvalidOptionException;
use Reynevan\CliOptions\Option;
use Reynevan\CliOptions\Options;

require __DIR__ . '/vendor/autoload.php';

$definitions = [
    new Option('port', 'p', 5353, description: 'Port to listen on'),
    new Option('address', 'a', '0.0.0.0', description: 'Bind address'),
    new Option('verbose', 'v', false, description: 'Verbose output', isFlag: true),
    new Option('help', 'h', false, description: 'Show help', isFlag: true),
];

try {
    $options = Options::parse($argv, $definitions);
} catch (InvalidOptionException $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(2);
}

$options->get('port');    // int(53)    for "-p 53", "-p53" or "--port=53"
$options->get('address'); // string     defaults to "0.0.0.0"
$options->get('verbose'); // bool(true) when "-v" or "--verbose" is present
$options->getArgs();      // string[]   positional arguments, e.g. file names
```

### Supported syntax

| Form                | Example              |
|---------------------|----------------------|
| long with value     | `--port 53`          |
| long with `=`       | `--port=53`          |
| short with value    | `-p 53`              |
| short, glued value  | `-p53`               |
| flag                | `-v`, `--verbose`    |
| combined flags      | `-hv`                |
| flags + value       | `-hp 53`, `-hp53`    |
| positional argument | `file.txt`, `-`      |
| end of options      | `--`                 |

### Option definition

```php
new Option(
    longName: 'port',       // used as the key in Options::get()
    shortName: 'p',         // optional single character
    default: 5353,          // value when the option is absent; also drives type casting
    description: '...',     // free text, for your own help output
    isFlag: false,          // true → option takes no value and yields bool(true)
    valueName: 'port',      // placeholder in help output: "-p/--port <port>" (default: "value")
);
```

Values are cast to the type of the default: `int`, `float` and `bool`
(`filter_var(..., FILTER_VALIDATE_BOOLEAN)`); anything else is kept as a string.

`Options::parse()` skips `$argv[0]` (the script name). An unknown option throws
`InvalidOptionException`.

### Positional arguments

Anything that is neither an option nor an option's value is collected as a
positional argument, in the order it appeared:

```php
// php index.php -p 1 -h arg1 arg2
$options->get('port');    // int(1)
$options->get('help');    // bool(true)
$options->getArgs();      // ['arg1', 'arg2']
$options->getArg(0);      // 'arg1'   (null when out of range)
```

Positional arguments may appear anywhere, including before or between options
(`index.php first -p 53 second`). A lone `-` is treated as an argument, since it
conventionally stands for stdin.

Everything after a `--` separator is taken as a positional argument verbatim,
even if it looks like an option:

```php
// php index.php -v -- --port=53 arg1
$options->get('verbose'); // bool(true)
$options->get('port');    // int(5353) — the default
$options->getArgs();      // ['--port=53', 'arg1']
```

### Help output

```php
use Reynevan\CliOptions\Help;

$help = new Help('php index.php [options]', $definitions, 'php index.php -p 53 -a 127.0.0.1');

if ($options->get('help')) {
    $help->print();   // or: echo $help->render();
    exit(0);
}
```

prints:

```
Usage: php index.php [options]
OPTIONS
  -p/--port <port>: Port to listen on
  -a/--address <value>: Bind address
  -v/--verbose: Verbose output
  -h/--help: Show help
EXAMPLE:
  php index.php -p 53 -a 127.0.0.1
```

## Development

```bash
composer install
composer test   # phpunit
composer cs     # phpcs (PSR-12)
composer stan   # phpstan level 6
```

## License

MIT — see [LICENSE](LICENSE).
