<?php

use CultuurNet\UDB3\JwtProvider\Factory\ConfigFactory;
use CultuurNet\UDB3\JwtProvider\Factory\ContainerFactory;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Error\ExceptionHandler;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use League\Route\Router;
use Slim\Psr7\Factory\ServerRequestFactory;
use Whoops\Run;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;

require_once __DIR__ . '/../vendor/autoload.php';

$config = ConfigFactory::create(__DIR__ . '/../');

$container = ContainerFactory::forWeb($config);

$whoops = new Run();
$whoops->prependHandler(
    new ExceptionHandler(
        new SapiStreamEmitter(),
        new SlimResponseFactory()
    )
);
$whoops->register();

$response = $container->get(Router::class)->dispatch(
    ServerRequestFactory::createFromGlobals()
);


(new SapiStreamEmitter())->emit($response);
