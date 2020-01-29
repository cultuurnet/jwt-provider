<?php declare(strict_types=1);


namespace CultuurNet\UDB3\JwtProvider\Factory;


use CultuurNet\UDB3\JwtProvider\Infrastructure\Error\ExceptionHandler;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Whoops\Run;
use Whoops\RunInterface;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;

class ErrorHandlerFactory
{
    public static function create() : RunInterface
    {

        $logger = new Logger('error');
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../../log/app.log', Logger::DEBUG));

        $whoops = new Run();
        $whoops->prependHandler(
            new ExceptionHandler(
                new SapiStreamEmitter(),
                new SlimResponseFactory(),
                $logger
            )
        );

        return $whoops;
    }
}
