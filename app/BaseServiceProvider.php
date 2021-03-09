<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Noodlehaus\Config;

abstract class BaseServiceProvider extends AbstractServiceProvider
{
    protected function add(string $serviceName, callable $function, ?string $tag = null): void
    {
        $definition = $this->getLeagueContainer()
            ->add($serviceName, $function);

        if ($tag !== null) {
            $definition->addTag($tag);
        }
    }

    protected function addShared(string $serviceName, callable $function, ?string $tag = null): void
    {
        $definition = $this->getLeagueContainer()
            ->share($serviceName, $function);

        if ($tag !== null) {
            $definition->addTag($tag);
        }
    }

    // @phpstan-ignore-next-line
    protected function parameter(string $parameter)
    {
        return $this->getContainer()->get(Config::class)->get($parameter);
    }

    // @phpstan-ignore-next-line
    protected function get(string $name)
    {
        return $this->getContainer()->get($name);
    }
}
