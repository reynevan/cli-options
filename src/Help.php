<?php

namespace Reynevan\CliOptions;

readonly class Help
{
    /**
     * @param string $usage e.g. "php index.php [options]"
     * @param Option[] $definitions
     * @param string|null $example e.g. "php index.php -p 53 -a 127.0.0.1"
     */
    public function __construct(
        private string $usage,
        private array $definitions,
        private ?string $example = null,
    ) {
    }

    public function render(): string
    {
        $lines = ['Usage: ' . $this->usage, 'OPTIONS'];

        foreach ($this->definitions as $option) {
            $lines[] = '  ' . self::formatOption($option);
        }

        if ($this->example !== null) {
            $lines[] = 'EXAMPLE:';
            $lines[] = '  ' . $this->example;
        }

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    public function print(): void
    {
        echo $this->render();
    }

    private static function formatOption(Option $option): string
    {
        $names = $option->getShortName() !== null
            ? sprintf('-%s/--%s', $option->getShortName(), $option->getLongName())
            : '--' . $option->getLongName();

        if (!$option->isFlag()) {
            $names .= sprintf(' <%s>', $option->getValueName());
        }

        if ($option->getDescription() === null) {
            return $names;
        }

        return $names . ': ' . $option->getDescription();
    }
}
