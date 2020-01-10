<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Factory;

use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Url;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

class SlimResponseFactory implements ResponseFactoryInterface
{

    public function badRequestWithMessage(string $message): ResponseInterface
    {
        $response = new Response(StatusCodeInterface::STATUS_BAD_REQUEST);
        $response->getBody()->write($message);
        return $response;
    }

    public function badRequest(): ResponseInterface
    {
        return new Response(StatusCodeInterface::STATUS_BAD_REQUEST);
    }

    public function redirectTo(Url $url): ResponseInterface
    {
        return new Response(
            StatusCodeInterface::STATUS_MOVED_PERMANENTLY,
            new Headers(
                ['Location' => $url->asString()]
            )
        );
    }
}
