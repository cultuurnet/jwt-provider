<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Factory;

use CultuurNet\UDB3\JwtProvider\Domain\Url;
use Psr\Http\Message\ResponseInterface;

interface ResponseFactoryInterface
{
    public function badRequestWithMessage(string $message): ResponseInterface;

    public function badRequest(): ResponseInterface;

    public function redirectTo(Url $url): ResponseInterface;
}
