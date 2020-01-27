<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Middleware;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\RefreshTokenNotAllowedException;
use CultuurNet\UDB3\JwtProvider\Domain\Middleware\AllowedRefresh;
use CultuurNet\UDB3\JwtProvider\Domain\Service\IsAllowedRefreshToken;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AllowedRefreshTest extends TestCase
{

    /**
     * @test
     */
    public function it_allows_requests_that_have_the_appropriate_group_id()
    {

        $serverRequest = $this->prophesize(ServerRequestInterface::class);

        $isAllowedRefreshService = $this->prophesize(IsAllowedRefreshToken::class);
        $isAllowedRefreshService->__invoke($serverRequest)->willReturn(true);

        $handler = $this->prophesize(RequestHandlerInterface::class);

        $handler->handle($serverRequest)->shouldBeCalled();

        $allowedRefreshMiddleware = new AllowedRefresh(
            $isAllowedRefreshService->reveal()
        );

        $allowedRefreshMiddleware->process($serverRequest->reveal(), $handler->reveal());
    }

    /**
     * @test
     */
    public function it_throws_exception_for_request_that_do_not_have_the_refresh_permission()
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);

        $isAllowedRefreshService = $this->prophesize(IsAllowedRefreshToken::class);
        $isAllowedRefreshService->__invoke($serverRequest)->willReturn(false);

        $handler = $this->prophesize(RequestHandlerInterface::class);

        $handler->handle($serverRequest)->shouldNotBeCalled();

        $allowedRefreshMiddleware = new AllowedRefresh(
            $isAllowedRefreshService->reveal()
        );


        $this->expectException(RefreshTokenNotAllowedException::class);

        $allowedRefreshMiddleware->process($serverRequest->reveal(), $handler->reveal());
    }
}
