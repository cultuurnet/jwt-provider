<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Factory;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

interface ResponseFactoryInterface
{
    public function redirectTo(UriInterface $url): ResponseInterface;

    public function responseWithToken(string $token): ResponseInterface;
}
