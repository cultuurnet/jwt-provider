<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Middleware;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\InvalidApiKeyException;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\RefreshTokenNotAllowedException;
use CultuurNet\UDB3\JwtProvider\Domain\Service\IsAllowedRefreshToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AllowedRefresh implements MiddlewareInterface
{
    /**
     * @var IsAllowedRefreshToken
     */
    private $isAllowedRefreshToken;

    public function __construct(
        IsAllowedRefreshToken $isAllowedRefreshToken
    ) {
        $this->isAllowedRefreshToken = $isAllowedRefreshToken;
    }

    /**
     * @inheritDoc
     * @throws InvalidApiKeyException
     * @throws RefreshTokenNotAllowedException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        if (!$this->isAllowedRefreshToken->__invoke($request)) {
            throw new RefreshTokenNotAllowedException();
        }

        return $handler->handle($request);
    }
}
