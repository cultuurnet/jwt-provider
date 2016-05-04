<?php

use CultuurNet\Clock\SystemClock;
use CultuurNet\UDB3\JwtProvider\CultureFeed\CultureFeedServiceProvider;
use CultuurNet\UDB3\JwtProvider\Jwt\JwtOAuthCallbackHandlerServiceProvider;
use CultuurNet\UDB3\JwtProvider\Jwt\JwtServiceProvider;
use CultuurNet\UDB3\JwtProvider\OAuth\OAuthServiceProvider;
use CultuurNet\UDB3\JwtProvider\OAuth\OAuthUrlHelperServiceProvider;
use CultuurNet\UDB3\JwtProvider\RequestTokenStorage\RequestTokenStorageServiceProvider;
use CultuurNet\UDB3\JwtProvider\User\CultureFeedUserServiceProvider;
use DerAlex\Silex\YamlConfigServiceProvider;
use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

$app = new Application();

if (!isset($appConfigLocation)) {
    $appConfigLocation =  __DIR__;
}
$app->register(new YamlConfigServiceProvider($appConfigLocation . '/config.yml'));

/**
 * Turn debug on or off.
 */
$app['debug'] = $app['config']['debug'] === true;

/**
 * Load additional bootstrap files.
 */
foreach ($app['config']['bootstrap'] as $identifier => $enabled) {
    if (true === $enabled) {
        require __DIR__ . "/bootstrap/{$identifier}.php";
    }
}

$app['clock'] = $app->share(
    function () {
        return new SystemClock(
            new DateTimeZone('Europe/Brussels')
        );
    }
);

$app->register(new SessionServiceProvider());
$app->register(new UrlGeneratorServiceProvider());

$app->register(new JwtOAuthCallbackHandlerServiceProvider());
$app->register(new CultureFeedServiceProvider());
$app->register(new CultureFeedUserServiceProvider());
$app->register(new RequestTokenStorageServiceProvider());
$app->register(new OAuthUrlHelperServiceProvider());
$app->register(new OAuthServiceProvider());
$app->register(new JwtServiceProvider());

return $app;
