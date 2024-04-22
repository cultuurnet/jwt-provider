<?php

use CultuurNet\UDB3\JwtProvider\Factory\ConfigFactory;
use CultuurNet\UDB3\JwtProvider\Factory\ContainerFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use League\Route\Router;
use Slim\Psr7\Factory\ServerRequestFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$config = ConfigFactory::create(__DIR__ . '/../');

$container = ContainerFactory::forWeb($config);

$response = $container->get(Router::class)->dispatch(
    ServerRequestFactory::createFromGlobals()
);

(new SapiStreamEmitter())->emit($response);
