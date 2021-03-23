<?php

declare(strict_types=1);

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\JwtProvider\Error\LoggerFactory;
use CultuurNet\UDB3\JwtProvider\Error\LoggerName;
use CultuurNet\UDB3\JwtProvider\Factory\ConfigFactory;
use CultuurNet\UDB3\JwtProvider\Factory\ContainerFactory;
use CultuurNet\UDB3\JwtProvider\Factory\ErrorHandlerFactory;
use League\Route\Router;
use Slim\Psr7\Factory\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;

require_once __DIR__ . '/../vendor/autoload.php';

$config = ConfigFactory::create(__DIR__ . '/../');
$container = ContainerFactory::forWeb($config);

$apiRequest = ServerRequestFactory::createFromGlobals();

$container->share(ApiKey::class, function () use ($container, $apiRequest) {
    $apiKeyReader = $container->get(ApiKeyReaderInterface::class);
    return $apiKeyReader->read($apiRequest);
});

$errorLogger = LoggerFactory::create($container, new LoggerName('web'));
if ($config->get('debug') === true) {
    $whoops = ErrorHandlerFactory::forWebDebug($errorLogger);
} else {
    $whoops = ErrorHandlerFactory::forWeb($errorLogger);
}
$whoops->register();

$response = $container->get(Router::class)->dispatch($apiRequest);

(new SapiStreamEmitter())->emit($response);
