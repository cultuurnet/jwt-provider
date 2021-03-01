<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Noodlehaus\Config;

abstract class BaseServiceProvider extends AbstractServiceProvider
{
    /**
     * Add Service definition to container
     *
     * @param $function
     */
    protected function add(string $serviceName, $function, ?string $tag = null): void
    {
        $definition = $this->getLeagueContainer()
            ->add($serviceName, $function);

        if ($tag !== null) {
            $definition->addTag($tag);
        }
    }

    /**
     * Add Service definition to container
     *
     * @param $function
     */
    protected function addShared(string $serviceName, $function, ?string $tag = null): void
    {
        $definition = $this->getLeagueContainer()
            ->share($serviceName, $function);

        if ($tag !== null) {
            $definition->addTag($tag);
        }
    }

    /**
     * Get parameter from config
     *
     */
    protected function parameter(string $parameter)
    {
        return $this->getContainer()->get(Config::class)->get($parameter);
    }

    /**
     * Get service from container
     *
     */
    protected function get(string $name)
    {
        return $this->getContainer()->get($name);
    }
}
