<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;
use CultuurNet\UDB3\JwtProvider\Domain\Service\RefreshServiceInterface;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RefreshTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_new_token()
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $authService = $this->prophesize(RefreshServiceInterface::class);
        $authService->token()->willReturn('token');
        $authService->renewToken()->shouldBeCalled();

        $refreshAction  = new Refresh(
            new SlimResponseFactory(),
            $authService->reveal()
        );

        $response  = $refreshAction->__invoke($serverRequest->reveal());
        $this->assertEquals(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertEquals('token', $response->getBody());
    }


    /**
     * @test
     */
    public function it_returns_invalid_request_response_for_failed_token_renewal(): void
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $authService = $this->prophesize(RefreshServiceInterface::class);

        $authService->renewToken()->willThrow(UnSuccessfulAuthException::class);

        $refreshAction  = new Refresh(
            new SlimResponseFactory(),
            $authService->reveal()
        );

        $response  = $refreshAction->__invoke($serverRequest->reveal());
        $this->assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }
}
