<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Middleware;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\RefreshTokenNotAllowedException;
use CultuurNet\UDB3\JwtProvider\Domain\Middleware\AllowedRefresh;
use CultuurNet\UDB3\JwtProvider\Domain\Service\IsAllowedRefreshTokenInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AllowedRefreshTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_allows_requests_that_have_permission(): void
    {
        $apiKey = new ApiKey('api-key');

        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $handler = $this->prophesize(RequestHandlerInterface::class);

        $apiKeyReader = $this->prophesize(ApiKeyReaderInterface::class);
        $apiKeyReader->read($serverRequest)->willReturn($apiKey);

        $isAllowedRefreshService = $this->prophesize(IsAllowedRefreshTokenInterface::class);
        $isAllowedRefreshService->__invoke($apiKey)->willReturn(true);

        $handler->handle($serverRequest)->shouldBeCalled();

        $allowedRefreshMiddleware = new AllowedRefresh(
            $isAllowedRefreshService->reveal(),
            $apiKeyReader->reveal()
        );

        $allowedRefreshMiddleware->process($serverRequest->reveal(), $handler->reveal());
    }

    /**
     * @test
     */
    public function it_throws_exception_for_request_that_do_not_have_the_refresh_permission(): void
    {
        $apiKey = new ApiKey('api-key');

        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $handler = $this->prophesize(RequestHandlerInterface::class);

        $apiKeyReader = $this->prophesize(ApiKeyReaderInterface::class);
        $apiKeyReader->read($serverRequest)->willReturn($apiKey);

        $isAllowedRefreshService = $this->prophesize(IsAllowedRefreshTokenInterface::class);
        $isAllowedRefreshService->__invoke($apiKey)->willReturn(false);

        $handler->handle($serverRequest)->shouldNotBeCalled();

        $allowedRefreshMiddleware = new AllowedRefresh(
            $isAllowedRefreshService->reveal(),
            $apiKeyReader->reveal()
        );

        $this->expectException(RefreshTokenNotAllowedException::class);

        $allowedRefreshMiddleware->process($serverRequest->reveal(), $handler->reveal());
    }


    /**
     * @test
     */
    public function it_throws_exception_for_request_that_do_not_have_the_api_key(): void
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $handler = $this->prophesize(RequestHandlerInterface::class);

        $apiKeyReader = $this->prophesize(ApiKeyReaderInterface::class);
        $apiKeyReader->read($serverRequest)->willReturn(null);

        $isAllowedRefreshService = $this->prophesize(IsAllowedRefreshTokenInterface::class);

        $handler->handle($serverRequest)->shouldNotBeCalled();

        $allowedRefreshMiddleware = new AllowedRefresh(
            $isAllowedRefreshService->reveal(),
            $apiKeyReader->reveal()
        );

        $this->expectException(RefreshTokenNotAllowedException::class);

        $allowedRefreshMiddleware->process($serverRequest->reveal(), $handler->reveal());
    }
}
