<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Factory;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\JwtProviderExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

interface ResponseFactoryInterface
{
    public function badRequestWithMessage(string $message): ResponseInterface;

    public function badRequest(): ResponseInterface;

    public function redirectTo(UriInterface $url): ResponseInterface;

    public function responseWithToken(string $token): ResponseInterface;

    public function forJwtProviderException(JwtProviderExceptionInterface $exception): ResponseInterface;

    public function internalServerError(): ResponseInterface;
}
