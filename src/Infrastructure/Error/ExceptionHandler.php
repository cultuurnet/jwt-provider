<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Error;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\JwtProviderExceptionInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use Whoops\Handler\Handler;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;

class ExceptionHandler extends Handler
{

    /**
     * @var EmitterInterface
     */
    private $emitter;

    /**
     * @var ResponseFactoryInterface
     */
    private $slimResponseFactory;


    public function __construct(EmitterInterface $emitter, ResponseFactoryInterface $slimResponseFactory)
    {
        $this->emitter = $emitter;
        $this->slimResponseFactory = $slimResponseFactory;
    }

    public function handle()
    {
        $exception = $this->getInspector()->getException();

        $response = $this->generateResponse($exception);

        $this->emitter->emit(
            $response
        );

        return Handler::QUIT;
    }

    private function generateResponse(\Throwable $exception)
    {
        if ($exception instanceof JwtProviderExceptionInterface) {
            return $this->slimResponseFactory->forJwtProviderException($exception);
        }

        return $this->slimResponseFactory->internalServerError();
    }
}
