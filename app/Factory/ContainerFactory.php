<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Factory;

use CultuurNet\UDB3\JwtProvider\ActionServiceProvider;
use CultuurNet\UDB3\JwtProvider\ApiGuardServiceProvider;
use CultuurNet\UDB3\JwtProvider\Error\SentryWebServiceProvider;
use CultuurNet\UDB3\JwtProvider\MiddlewareServiceProvider;
use CultuurNet\UDB3\JwtProvider\RoutingServiceProvider;
use CultuurNet\UDB3\JwtProvider\Error\SentryHubServiceProvider;
use CultuurNet\UDB3\JwtProvider\SessionServiceProvider;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Noodlehaus\Config;

final class ContainerFactory
{
    public static function forWeb(Config $config): Container
    {
        $container = self::build($config);

        $container->addServiceProvider(SentryHubServiceProvider::class);
        $container->addServiceProvider(SentryWebServiceProvider::class);
        $container->addServiceProvider(RoutingServiceProvider::class);
        $container->addServiceProvider(ActionServiceProvider::class);
        $container->addServiceProvider(ApiGuardServiceProvider::class);
        $container->addServiceProvider(MiddlewareServiceProvider::class);
        $container->addServiceProvider(SessionServiceProvider::class);

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
