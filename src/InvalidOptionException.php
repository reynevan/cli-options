<?php

namespace Reynevan\CliOptions;

class InvalidOptionException extends \Exception
{
    public function __construct(string $option)
    {
        parent::__construct(sprintf(
            'The "%s" option is invalid.',
            $option
        ));
    }
}
