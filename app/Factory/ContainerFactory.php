<?php

namespace CultuurNet\UDB3\JwtProvider\Factory;

use CultuurNet\UDB3\JwtProvider\ActionServiceProvider;
use CultuurNet\UDB3\JwtProvider\ApiGuardServiceProvider;
use CultuurNet\UDB3\JwtProvider\RoutingServiceProvider;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Noodlehaus\Config;

class ContainerFactory
{
    public static function forWeb(Config $config): Container
    {
        $container = self::build($config);

        $container->addServiceProvider(RoutingServiceProvider::class);
        $container->addServiceProvider(ActionServiceProvider::class);
        $container->addServiceProvider(ApiGuardServiceProvider::class);

        return $container;
    }

    private static function build(Config $config): Container
    {
        $container = new Container();
        $container->delegate(new ReflectionContainer());
        $container->add(
            Config::class,
            $config
        );

        return $container;
    }
}
