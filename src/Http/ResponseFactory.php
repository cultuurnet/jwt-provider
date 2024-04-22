<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Http;

use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

final class ResponseFactory
{
    /**
     * @param string[] $headers
     */
    public static function create(int $statusCode, string $body = '', array $headers = []): ResponseInterface
    {
        $response = new Response($statusCode, new Headers($headers));
        $response->getBody()->write($body);
        return $response;
    }
}
