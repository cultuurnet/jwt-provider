<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use Whoops\Handler\Handler;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Error\ExceptionHandler;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Whoops\Run;
use Whoops\RunInterface;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;

class ErrorHandlerServiceProvider extends BaseServiceProvider
{
    protected $provides = [
        RunInterface::class,
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->add(
            RunInterface::class,
            function () {
                $whoops = new Run();
                $whoops->prependHandler(new ExceptionHandler(
                        new SapiStreamEmitter(),
                        new SlimResponseFactory()
                    )
                );
                return $whoops;
            }
        );
    }
}
