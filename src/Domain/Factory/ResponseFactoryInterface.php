<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Factory;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

interface ResponseFactoryInterface
{
    public function badRequestWithMessage(string $message): ResponseInterface;

    public function badRequest(): ResponseInterface;

    public function redirectTo(UriInterface $url): ResponseInterface;
}
