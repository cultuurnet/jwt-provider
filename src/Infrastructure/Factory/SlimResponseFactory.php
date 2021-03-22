<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Factory;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\JwtProviderExceptionInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

final class SlimResponseFactory implements ResponseFactoryInterface
{
    public function redirectTo(UriInterface $url): ResponseInterface
    {
        return new Response(
            StatusCodeInterface::STATUS_MOVED_PERMANENTLY,
            new Headers(
                ['Location' => $url->__toString()]
            )
        );
    }

    public function responseWithToken(string $token): ResponseInterface
    {
        $response = new Response(
            StatusCodeInterface::STATUS_OK
        );
        $body = $response->getBody();
        $body->write($token);
        return $response->withBody($body);
    }

    public function forJwtProviderException(JwtProviderExceptionInterface $exception): ResponseInterface
    {
        $response = new Response(StatusCodeInterface::STATUS_BAD_REQUEST);
        $response->getBody()->write($exception->getMessage());
        return $response;
    }

    public function internalServerError(): ResponseInterface
    {
        $response = new Response(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        $response->getBody()->write('An internal server error occurred.');
        return $response;
    }
}
