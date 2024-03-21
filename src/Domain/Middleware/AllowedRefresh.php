<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Middleware;

use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\RefreshTokenNotAllowedException;
use CultuurNet\UDB3\JwtProvider\Domain\Service\IsAllowedRefreshTokenInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AllowedRefresh implements MiddlewareInterface
{
    private \CultuurNet\UDB3\JwtProvider\Domain\Service\IsAllowedRefreshTokenInterface $isAllowedRefreshToken;

    private \CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface $apiKeyReader;

    public function __construct(
        IsAllowedRefreshTokenInterface $isAllowedRefreshToken,
        ApiKeyReaderInterface $apiKeyReader
    ) {
        $this->isAllowedRefreshToken = $isAllowedRefreshToken;
        $this->apiKeyReader = $apiKeyReader;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $apiKey = $this->apiKeyReader->read($request);

        if ($apiKey === null || !$this->isAllowedRefreshToken->__invoke($apiKey)) {
            throw new RefreshTokenNotAllowedException();
        }

        return $handler->handle($request);
    }
}
