<?php

namespace Reynevan\CliOptions;

class MissingOptionValueException extends \Exception
{
    public function __construct(Option $option)
    {
        parent::__construct(sprintf(
            'The "--%s" option requires a value.',
            $option->getLongName()
        ));
    }
}
