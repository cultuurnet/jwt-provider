<?php

require_once __DIR__ . '/../vendor/autoload.php';

use CultuurNet\UDB3\JwtProvider\Jwt\JwtOAuthCallbackHandlerServiceProvider;
use CultuurNet\UDB3\JwtProvider\OAuth\OAuthControllerProvider;
use CultuurNet\UDB3\JwtProvider\OAuth\OAuthServiceProvider;
use CultuurNet\UDB3\JwtProvider\OAuth\OAuthUrlHelperServiceProvider;
use CultuurNet\UDB3\JwtProvider\User\CultureFeedUserServiceProvider;
use CultuurNet\UDB3\JwtProvider\RequestTokenStorage\RequestTokenStorageServiceProvider;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

/** @var Application $app */
$app = require __DIR__ . '/../bootstrap.php';

/**
 * Allow to use services as controllers.
 */
$app->register(new ServiceControllerServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new UrlGeneratorServiceProvider());

$app->register(new JwtOAuthCallbackHandlerServiceProvider());
$app->register(new CultureFeedUserServiceProvider());
$app->register(new RequestTokenStorageServiceProvider());
$app->register(new OAuthUrlHelperServiceProvider());
$app->register(new OAuthServiceProvider());

$app->mount('culturefeed/oauth', new OAuthControllerProvider());

$app->run();
