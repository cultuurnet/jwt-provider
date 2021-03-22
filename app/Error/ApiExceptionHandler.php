<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Error;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\JwtProviderExceptionInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use Whoops\Handler\Handler;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;

final class ApiExceptionHandler extends Handler
{
    /**
     * @var EmitterInterface
     */
    private $emitter;

    public function __construct(EmitterInterface $emitter) {
        $this->emitter = $emitter;
    }

    public function handle()
    {
        $exception = $this->getInspector()->getException();

        $response = $this->generateResponse($exception);

        $this->emitter->emit($response);

        return Handler::QUIT;
    }

    private function generateResponse(\Throwable $exception): ResponseInterface
    {
        if ($exception instanceof JwtProviderExceptionInterface) {
            $response = new Response(StatusCodeInterface::STATUS_BAD_REQUEST);
            $response->getBody()->write($exception->getMessage());
            return $response;
        }

        $response = new Response(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        $response->getBody()->write('An internal server error occurred.');
        return $response;
    }
}
