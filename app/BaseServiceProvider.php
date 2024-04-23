<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Noodlehaus\Config;

abstract class BaseServiceProvider extends AbstractServiceProvider
{
    /**
     * Add Service definition to container
     */
    protected function add(string $serviceName, callable $function, ?string $tag = null): void
    {
        $definition = $this->getContainer()
            ->add($serviceName, $function);

        if ($tag !== null) {
            $definition->addTag($tag);
        }
    }

    /**
     * Add Service definition to container
     */
    protected function addShared(string $serviceName, callable $function, ?string $tag = null): void
    {
        $definition = $this->getContainer()
            ->addShared($serviceName, $function);

        if ($tag !== null) {
            $definition->addTag($tag);
        }
    }

    /**
     * Get parameter from config
     *
     * @return mixed
     */
    protected function parameter(string $parameter)
    {
        return $this->getContainer()->get(Config::class)->get($parameter);
    }

    /**
     * Get service from container
     *
     * @return mixed
     */
    protected function get(string $name)
    {
        return $this->getContainer()->get($name);
    }
}
