<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\BadRequestException;
use CultuurNet\UDB3\JwtProvider\Domain\Service\RefreshServiceInterface;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class RefreshTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_new_token(): void
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $serverRequest->getQueryParams()->willReturn(
            [
                'refresh' => 'refresh-token',
            ]
        );
        $authService = $this->prophesize(RefreshServiceInterface::class);
        $authService->token('refresh-token')->willReturn('new-token');

        $refreshAction = new Refresh(
            new SlimResponseFactory(),
            $authService->reveal()
        );

        $response = $refreshAction->__invoke($serverRequest->reveal());
        $this->assertEquals(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertEquals('new-token', $response->getBody());
    }

    /**
     * @test
     */
    public function it_throws_exception_if_request_is_missing_a_refresh_token(): void
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $serverRequest->getQueryParams()->willReturn([]);

        $authService = $this->prophesize(RefreshServiceInterface::class);

        $refreshAction = new Refresh(
            new SlimResponseFactory(),
            $authService->reveal()
        );
        $this->expectException(BadRequestException::class);
        $refreshAction->__invoke($serverRequest->reveal());
    }
}
