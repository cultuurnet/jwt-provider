<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Error;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\JwtProviderExceptionInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use Monolog\Logger;
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
    /**
     * @var Logger
     */
    private $logger;


    public function __construct(
        EmitterInterface $emitter,
        ResponseFactoryInterface $slimResponseFactory,
        Logger $logger
    ) {
        $this->emitter = $emitter;
        $this->slimResponseFactory = $slimResponseFactory;
        $this->logger = $logger;
    }

    public function handle()
    {
        $exception = $this->getInspector()->getException();

        $response = $this->generateResponse($exception);

        $this->logError($exception);

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

    /**
     * @param \Throwable $exception
     */
    private function logError(\Throwable $exception): void
    {
        $log = [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ];

        if ($exception instanceof JwtProviderExceptionInterface) {
            $this->logger->warning(
                get_class($exception),
                $log
            );
            return;
        }

        $this->logger->error(
            get_class($exception),
            $log
        );
    }
}
