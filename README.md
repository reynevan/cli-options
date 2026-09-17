# reynevan/cli-options

Tiny, dependency-free command-line options parser for PHP 8.4+.

It does one thing: turns `$argv` into a set of typed values based on a list of
option definitions. No commands, no sub-commands, no help generation — just
options.

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

### Option definition

```php
new Option(
    longName: 'port',       // used as the key in Options::get()
    shortName: 'p',         // optional single character
    default: 5353,          // value when the option is absent; also drives type casting
    description: '...',     // free text, for your own help output
    isFlag: false,          // true → option takes no value and yields bool(true)
);
```

Values are cast to the type of the default: `int`, `float` and `bool`
(`filter_var(..., FILTER_VALIDATE_BOOLEAN)`); anything else is kept as a string.

`Options::parse()` skips `$argv[0]` (the script name). An unknown option throws
`InvalidOptionException`.

## Development

```bash
composer install
composer test   # phpunit
composer cs     # phpcs (PSR-12)
composer stan   # phpstan level 6
```

## License

MIT — see [LICENSE](LICENSE).
