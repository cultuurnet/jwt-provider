<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Unit\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Action\RequestToken;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoDestinationPresent;
use CultuurNet\UDB3\JwtProvider\Domain\Url;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepository;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthService;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractDestinationUrlFromRequest;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RequestTokenTest extends TestCase
{

    /**
     * @test
     */
    public function it_requests_for_token()
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $destinationUrl = Url::fromString('http://foo-bar.com');

        $extractDestinationUrlFromRequest = $this->prophesize(ExtractDestinationUrlFromRequest::class);
        $extractDestinationUrlFromRequest->__invoke($serverRequest)->willReturn($destinationUrl);

        $destinationUrlRepository = $this->prophesize(DestinationUrlRepository::class);
        $destinationUrlRepository->storeDestinationUrl($destinationUrl)->shouldBeCalled();

        $externalAuthService = $this->prophesize(AuthService::class);
        $externalAuthService->redirectToLogin()->shouldBeCalled();

        $requestTokenAction = new RequestToken(
            $extractDestinationUrlFromRequest->reveal(),
            $destinationUrlRepository->reveal(),
            $externalAuthService->reveal()
        );

        $requestTokenAction->__invoke($serverRequest->reveal());
    }

    /**
     * @test
     */
    public function it_returns_bad_request_for_no_destination_present()
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);

        $extractDestinationUrlFromRequest = $this->prophesize(ExtractDestinationUrlFromRequest::class);
        $extractDestinationUrlFromRequest->__invoke($serverRequest)->willThrow(NoDestinationPresent::class);

        $destinationUrlRepository = $this->prophesize(DestinationUrlRepository::class);

        $externalAuthService = $this->prophesize(AuthService::class);
        $externalAuthService->redirectToLogin()->shouldNotBeCalled();

        $requestTokenAction = new RequestToken(
            $extractDestinationUrlFromRequest->reveal(),
            $destinationUrlRepository->reveal(),
            $externalAuthService->reveal()
        );

        $response = $requestTokenAction->__invoke($serverRequest->reveal());
        $this->assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(NoDestinationPresent::MESSAGE, $response->getBody());
    }
}
