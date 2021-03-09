<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Factory;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Error\ExceptionHandler;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Error\SentryExceptionHandler;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Sentry\State\HubInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\RunInterface;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;

final class ErrorHandlerFactory
{
    public static function create(HubInterface $sentryHub, ?ApiKey $apiKey, bool $isDebugEnvironment): RunInterface
    {
        $logger = new Logger('error');
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../../log/app.log', Logger::DEBUG));

        $whoops = new Run();

        if ($isDebugEnvironment === true) {
            $whoops->prependHandler(new PrettyPageHandler());
            return $whoops;
        }

        $whoops->prependHandler(
            new ExceptionHandler(
                new SapiStreamEmitter(),
                new SlimResponseFactory(),
                $logger
            )
        );

        $whoops->prependHandler(
            new SentryExceptionHandler(
                $sentryHub,
                $apiKey
            )
        );

        return $whoops;
    }
}
