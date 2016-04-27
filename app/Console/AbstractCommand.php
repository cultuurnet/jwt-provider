<?php

namespace CultuurNet\UDB3\JwtProvider\Console;

use Knp\Command\Command;

abstract class AbstractCommand extends Command
{
    /**
     * @param string $name
     * @param string $expectedType
     */
    protected function getService($name, $expectedType)
    {
        $app = $this->getSilexApplication();
        $service = $app[$name];

        if (!($service instanceof $expectedType)) {
            throw new \RuntimeException("{$name} is not of type {$expectedType}.");
        }

        return $service;
    }
}
