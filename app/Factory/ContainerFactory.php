<?php

namespace CultuurNet\UDB3\JwtProvider\Factory;

use CultuurNet\UDB3\JwtProvider\CommandServiceProvider;
use CultuurNet\UDB3\JwtProvider\CultureFeed\CultureFeedServiceProvider;
use CultuurNet\UDB3\JwtProvider\Jwt\JwtServiceProvider;
use CultuurNet\UDB3\JwtProvider\OAuth\OAuthServiceProvider;
use CultuurNet\UDB3\JwtProvider\RequestTokenStorage\RequestTokenStorageServiceProvider;
use CultuurNet\UDB3\JwtProvider\RoutingServiceProvider;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Noodlehaus\Config;

class ContainerFactory
{
    public static function forCli(Config $config): Container
    {
        $container = self::build($config);
        $container->addServiceProvider(new CommandServiceProvider);
        return $container;
    }
    public static function forWeb(Config $config): Container
    {
        $container = self::build($config);
        $container->addServiceProvider(new RoutingServiceProvider);
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

        $container->addServiceProvider(new CultureFeedServiceProvider);
        $container->addServiceProvider(new JwtServiceProvider);
        $container->addServiceProvider(new OAuthServiceProvider);
        $container->addServiceProvider(new RequestTokenStorageServiceProvider);

        return $container;
    }
}
