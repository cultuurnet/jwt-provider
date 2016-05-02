<?php

use CultuurNet\Clock\SystemClock;
use CultuurNet\UDB3\JwtProvider\Jwt\JwtServiceProvider;
use DerAlex\Silex\YamlConfigServiceProvider;
use Silex\Application;

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

$app->register(new JwtServiceProvider());

return $app;
