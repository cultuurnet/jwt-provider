<?php

require_once __DIR__ . '/../vendor/autoload.php';

use CultuurNet\UDB3\JwtProvider\OAuth\OAuthControllerProvider;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;

/** @var Application $app */
$app = require __DIR__ . '/../bootstrap.php';

/**
 * Allow to use services as controllers.
 */
$app->register(new ServiceControllerServiceProvider());

$app->mount('culturefeed/oauth', new OAuthControllerProvider());

$app->run();
