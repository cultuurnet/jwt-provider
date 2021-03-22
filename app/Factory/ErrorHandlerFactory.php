<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Factory;

use CultuurNet\UDB3\JwtProvider\Error\ErrorLoggerHandler;
use CultuurNet\UDB3\JwtProvider\Error\ApiExceptionHandler;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Psr\Log\LoggerInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\RunInterface;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;

final class ErrorHandlerFactory
{
    public static function forWeb(LoggerInterface $logger): RunInterface
    {
        $whoops = new Run();
        $whoops->prependHandler(new ApiExceptionHandler(new SapiStreamEmitter(), new SlimResponseFactory()));
        $whoops->prependHandler(new ErrorLoggerHandler($logger));
        return $whoops;
    }

    public static function forWebDebug(LoggerInterface $logger): RunInterface
    {
        $whoops = new Run();
        $whoops->prependHandler(new PrettyPageHandler());
        $whoops->prependHandler(new ErrorLoggerHandler($logger));
        return $whoops;
    }
}
