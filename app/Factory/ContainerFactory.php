<?php

namespace CultuurNet\UDB3\JwtProvider\Factory;

use CultuurNet\UDB3\JwtProvider\ActionServiceProvider;
use CultuurNet\UDB3\JwtProvider\CommandServiceProvider;
use CultuurNet\UDB3\JwtProvider\Jwt\JwtServiceProvider;
use CultuurNet\UDB3\JwtProvider\RoutingServiceProvider;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Noodlehaus\Config;

class ContainerFactory
{
    public static function forCli(Config $config): Container
    {
        $container = self::build($config);
        $container->addServiceProvider(CommandServiceProvider::class);
        $container->addServiceProvider(JwtServiceProvider::class);
        return $container;
    }

    public static function forWeb(Config $config): Container
    {
        $container = self::build($config);

        $container->addServiceProvider(RoutingServiceProvider::class);
        $container->addServiceProvider(ActionServiceProvider::class);

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
