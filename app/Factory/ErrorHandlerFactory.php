<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Factory;

use CultuurNet\UDB3\JwtProvider\Error\ErrorLoggerHandler;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Error\ExceptionHandler;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Psr\Log\LoggerInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\RunInterface;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;

final class ErrorHandlerFactory
{
    public static function create(LoggerInterface $logger, bool $isDebugEnvironment): RunInterface
    {
        $whoops = new Run();

        if ($isDebugEnvironment === true) {
            $whoops->prependHandler(new PrettyPageHandler());
        } else {
            $whoops->prependHandler(new ExceptionHandler(new SapiStreamEmitter(), new SlimResponseFactory()));
        }

        $whoops->prependHandler(new ErrorLoggerHandler($logger));

        return $whoops;
    }
}
