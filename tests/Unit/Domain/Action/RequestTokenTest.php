<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Unit\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Action\RequestToken;
use CultuurNet\UDB3\JwtProvider\Domain\DestinationUrl;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepository;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthService;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractDestinationUrlFromRequest;
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
        $destinationUrl = DestinationUrl::fromString('http://foo-bar.com');

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
}
