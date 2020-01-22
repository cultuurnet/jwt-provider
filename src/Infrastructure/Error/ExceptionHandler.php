<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Error;

use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Whoops\Handler\Handler;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;

class ExceptionHandler extends Handler
{

    /**
     * @var EmitterInterface
     */
    private $emitter;
    /**
     * @var SlimResponseFactory
     */
    private $slimResponseFactory;


    public function __construct(EmitterInterface $emitter, SlimResponseFactory $slimResponseFactory)
    {
        $this->emitter = $emitter;
        $this->slimResponseFactory = $slimResponseFactory;
    }

    public function handle()
    {
        $exception = $this->getInspector()->getException();

        $this->emitter->emit(
            $this->slimResponseFactory->badRequestWithMessage($exception->getMessage())
        );

        return Handler::QUIT;
    }
}
