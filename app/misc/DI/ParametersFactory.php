<?php

declare(strict_types=1);

namespace App\Misc;

use Nette\DI\Container;

class ContextParametersFactory
{
    /** @var array */
    private $parameters = [];


    public function __construct(Container $container)
    {
        $this->parameters = $container->parameters;
    }


    public function getParameter(string $key = null)
    {
        if (!$key) {
            return $this->parameters;
        }

        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }

        return null;
    }
}
