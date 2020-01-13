<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Unit\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Action\RequestToken;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\InvalidDestination;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoDestinationPresent;
use CultuurNet\UDB3\JwtProvider\Domain\Url;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepository;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthService;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractDestinationUrlFromRequest;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\UriFactory;

class RequestTokenTest extends TestCase
{

    /**
     * @test
     */
    public function it_requests_for_token()
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $destinationUrl = (new UriFactory())->createUri('http://foo-bar.com');

        $extractDestinationUrlFromRequest = $this->prophesize(ExtractDestinationUrlFromRequest::class);
        $extractDestinationUrlFromRequest->__invoke($serverRequest)->willReturn($destinationUrl);

        $destinationUrlRepository = $this->prophesize(DestinationUrlRepository::class);
        $destinationUrlRepository->storeDestinationUrl($destinationUrl)->shouldBeCalled();

        $externalAuthService = $this->prophesize(AuthService::class);
        $externalAuthService->redirectToLogin()->shouldBeCalled();

        $requestTokenAction = new RequestToken(
            $extractDestinationUrlFromRequest->reveal(),
            $destinationUrlRepository->reveal(),
            $externalAuthService->reveal(),
            new SlimResponseFactory()
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
            $externalAuthService->reveal(),
            new SlimResponseFactory()

        );

        $response = $requestTokenAction->__invoke($serverRequest->reveal());
        $this->assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(NoDestinationPresent::MESSAGE, $response->getBody());
    }


    /**
     * @test
     */
    public function it_returns_bad_request_for_no_invalid_destination()
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);

        $extractDestinationUrlFromRequest = $this->prophesize(ExtractDestinationUrlFromRequest::class);
        $extractDestinationUrlFromRequest->__invoke($serverRequest)->willThrow(InvalidDestination::class);

        $destinationUrlRepository = $this->prophesize(DestinationUrlRepository::class);

        $externalAuthService = $this->prophesize(AuthService::class);
        $externalAuthService->redirectToLogin()->shouldNotBeCalled();

        $requestTokenAction = new RequestToken(
            $extractDestinationUrlFromRequest->reveal(),
            $destinationUrlRepository->reveal(),
            $externalAuthService->reveal(),
            new SlimResponseFactory()
        );

        $response = $requestTokenAction->__invoke($serverRequest->reveal());

        $this->assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }
}
