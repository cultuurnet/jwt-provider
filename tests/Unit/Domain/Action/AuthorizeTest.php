<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Unit\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Action\Authorize;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuth;
use CultuurNet\UDB3\JwtProvider\Domain\Service\GenerateAuthorizedDestinationUrl;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepository;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthService;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\UriFactory;

class AuthorizeTest extends TestCase
{

    /**
     * @test
     */
    public function it_returns_response_with_authorized_url_for_successful_authorization()
    {
        $destinationUrlRepository = $this->prophesize(DestinationUrlRepository::class);
        $destinationUrl = (new UriFactory())->createUri('http://foo-bar.com/');

        $destinationUrlRepository->getDestinationUrl()->willReturn($destinationUrl);

        $authService = $this->prophesize(AuthService::class);
        $authService->token()->willReturn('token');

        $generateDestinationUrl = $this->prophesize(GenerateAuthorizedDestinationUrl::class);
        $generateDestinationUrl->__invoke($destinationUrl, 'token')
            ->willReturn((new UriFactory())->createUri('http://foo-bar.com?jwt=token'));

        $authorizeAction = new Authorize(
            $authService->reveal(),
            $destinationUrlRepository->reveal(),
            $generateDestinationUrl->reveal(),
            new SlimResponseFactory()
        );

        $response = $authorizeAction->__invoke();
        $this->assertEquals('http://foo-bar.com/?jwt=token', $response->getHeaderLine('Location'));
    }

    /**
     * @test
     */
    public function it_returns_invalid_request_response_for_un_successful_authorization()
    {
        $destinationUrlRepository = $this->prophesize(DestinationUrlRepository::class);
        $destinationUrl = (new UriFactory())->createUri('http://foo-bar.com/');

        $destinationUrlRepository->getDestinationUrl()->willReturn($destinationUrl);

        $authService = $this->prophesize(AuthService::class);
        $authService->token()->willReturn(null);

        $generateDestinationUrl = $this->prophesize(GenerateAuthorizedDestinationUrl::class);

        $authorizeAction = new Authorize(
            $authService->reveal(),
            $destinationUrlRepository->reveal(),
            $generateDestinationUrl->reveal(),
            new SlimResponseFactory()
        );

        $response = $authorizeAction->__invoke();
        $this->assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }


    /**
     * @test
     */
    public function it_returns_invalid_request_response_for_no_destination_url_present()
    {
        $destinationUrlRepository = $this->prophesize(DestinationUrlRepository::class);

        $destinationUrlRepository->getDestinationUrl()->willReturn(null);

        $authService = $this->prophesize(AuthService::class);
        $authService->token()->willReturn('token');

        $generateDestinationUrl = $this->prophesize(GenerateAuthorizedDestinationUrl::class);

        $authorizeAction = new Authorize(
            $authService->reveal(),
            $destinationUrlRepository->reveal(),
            $generateDestinationUrl->reveal(),
            new SlimResponseFactory()
        );

        $response = $authorizeAction->__invoke();
        $this->assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }


    /**
     * @test
     */
    public function it_returns_invalid_request_response_for_failed_authorization()
    {
        $destinationUrlRepository = $this->prophesize(DestinationUrlRepository::class);

        $authService = $this->prophesize(AuthService::class);
        $authService->token()->willThrow(UnSuccessfulAuth::class);

        $generateDestinationUrl = $this->prophesize(GenerateAuthorizedDestinationUrl::class);

        $authorizeAction = new Authorize(
            $authService->reveal(),
            $destinationUrlRepository->reveal(),
            $generateDestinationUrl->reveal(),
            new SlimResponseFactory()
        );

        $response = $authorizeAction->__invoke();
        $this->assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }
}
