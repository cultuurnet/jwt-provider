<?php declare(strict_types=1);


namespace CultuurNet\UDB3\JwtProvider\Factory;


use CultuurNet\UDB3\JwtProvider\Infrastructure\Error\ExceptionHandler;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Whoops\Run;
use Whoops\RunInterface;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;

class ErrorHandlerFactory
{
    public static function create() : RunInterface
    {
        $whoops = new Run();
        $whoops->prependHandler(
            new ExceptionHandler(
                new SapiStreamEmitter(),
                new SlimResponseFactory()
            )
        );

        return $whoops;
    }
}
