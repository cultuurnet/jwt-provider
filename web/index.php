<?php

declare(strict_types=1);

use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\JwtProvider\Factory\ConfigFactory;
use CultuurNet\UDB3\JwtProvider\Factory\ContainerFactory;
use CultuurNet\UDB3\JwtProvider\Factory\ErrorHandlerFactory;
use League\Route\Router;
use Sentry\State\HubInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;

require_once __DIR__ . '/../vendor/autoload.php';

$config = ConfigFactory::create(__DIR__ . '/../');
$container = ContainerFactory::forWeb($config);

$apiRequest = ServerRequestFactory::createFromGlobals();

$whoops = ErrorHandlerFactory::create(
    $container->get(HubInterface::class),
    $container->get(ApiKeyReaderInterface::class)->read($apiRequest),
    false
);
$whoops->register();

$response = $container->get(Router::class)->dispatch($apiRequest);

(new SapiStreamEmitter())->emit($response);
