<?php

use CultuurNet\UDB3\JwtProvider\Factory\ConfigFactory;
use CultuurNet\UDB3\JwtProvider\Factory\ContainerFactory;
use League\Route\Router;
use Slim\Psr7\Factory\ServerRequestFactory;
use Whoops\RunInterface;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;

require_once __DIR__ . '/../vendor/autoload.php';

$config = ConfigFactory::create(__DIR__ . '/../');

$container = ContainerFactory::forWeb($config);

$errorHandler = $container->get(RunInterface::class);
$errorHandler->register();

$response = $container->get(Router::class)->dispatch(
    ServerRequestFactory::createFromGlobals()
);

(new SapiStreamEmitter())->emit($response);
