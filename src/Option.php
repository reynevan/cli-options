<?php

namespace Reynevan\CliOptions;

readonly class Option
{
    public function __construct(
        private string $longName,
        private ?string $shortName = null,
        private mixed $default = null,
        private ?string $description = null,
        private bool $isFlag = false,
        private string $valueName = 'value',
    ) {
    }

    public function getLongName(): string
    {
        return $this->longName;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isFlag(): bool
    {
        return $this->isFlag;
    }

    /**
     * Placeholder shown in help output for the option's value, e.g. "port" in "-p/--port <port>".
     */
    public function getValueName(): string
    {
        return $this->valueName;
    }
}
