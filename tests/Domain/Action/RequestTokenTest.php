<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\InvalidDestinationException;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoDestinationPresentException;
use CultuurNet\UDB3\JwtProvider\Domain\Url;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthServiceInterface;
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
    public function it_requests_for_token(): void
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $destinationUrl = (new UriFactory())->createUri('http://foo-bar.com');

        $extractDestinationUrlFromRequest = $this->prophesize(ExtractDestinationUrlFromRequest::class);
        $extractDestinationUrlFromRequest->__invoke($serverRequest)->willReturn($destinationUrl);

        $destinationUrlRepository = $this->prophesize(DestinationUrlRepositoryInterface::class);
        $destinationUrlRepository->storeDestinationUrl($destinationUrl)->shouldBeCalled();

        $externalAuthService = $this->prophesize(AuthServiceInterface::class);
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
    public function it_returns_bad_request_for_no_destination_present(): void
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);

        $extractDestinationUrlFromRequest = $this->prophesize(ExtractDestinationUrlFromRequest::class);
        $extractDestinationUrlFromRequest->__invoke($serverRequest)->willThrow(NoDestinationPresentException::class);

        $destinationUrlRepository = $this->prophesize(DestinationUrlRepositoryInterface::class);

        $externalAuthService = $this->prophesize(AuthServiceInterface::class);
        $externalAuthService->redirectToLogin()->shouldNotBeCalled();

        $requestTokenAction = new RequestToken(
            $extractDestinationUrlFromRequest->reveal(),
            $destinationUrlRepository->reveal(),
            $externalAuthService->reveal(),
            new SlimResponseFactory()
        );

        $response = $requestTokenAction->__invoke($serverRequest->reveal());
        $this->assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(NoDestinationPresentException::MESSAGE, $response->getBody());
    }


    /**
     * @test
     */
    public function it_returns_bad_request_for_no_invalid_destination(): void
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);

        $extractDestinationUrlFromRequest = $this->prophesize(ExtractDestinationUrlFromRequest::class);
        $extractDestinationUrlFromRequest->__invoke($serverRequest)->willThrow(\InvalidArgumentException::class);

        $destinationUrlRepository = $this->prophesize(DestinationUrlRepositoryInterface::class);

        $externalAuthService = $this->prophesize(AuthServiceInterface::class);
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
