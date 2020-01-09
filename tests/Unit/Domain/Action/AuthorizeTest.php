<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Unit\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Action\Authorize;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoTokenPresent;
use CultuurNet\UDB3\JwtProvider\Domain\Service\GenerateAuthorizedDestinationUrl;
use CultuurNet\UDB3\JwtProvider\Domain\Url;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepository;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthService;
use PHPUnit\Framework\TestCase;

class AuthorizeTest extends TestCase
{

    /**
     * @test
     */
    public function it_returns_authorized_destination_url_for_successful_authorization()
    {
        $destinationUrlRepository = $this->prophesize(DestinationUrlRepository::class);
        $destinationUrl = Url::fromString('http://foo-bar.com');

        $destinationUrlRepository->getDestinationUrl()->willReturn($destinationUrl);

        $authService = $this->prophesize(AuthService::class);
        $authService->token()->willReturn('token');

        $generateDestinationUrl = $this->prophesize(GenerateAuthorizedDestinationUrl::class);
        $generateDestinationUrl->__invoke($destinationUrl, 'token')
            ->willReturn(Url::fromString('http://foo-bar.com?jwt=token'));

        $authorizeAction = new Authorize(
            $authService->reveal(),
            $destinationUrlRepository->reveal(),
            $generateDestinationUrl->reveal()
        );

        $redirectResponse = $authorizeAction->__invoke();
        $this->assertEquals('http://foo-bar.com?jwt=token', $redirectResponse->asString());
    }

    /**
     * @test
     */
    public function it_throws_exception_for_un_successful_authorization()
    {
        $destinationUrlRepository = $this->prophesize(DestinationUrlRepository::class);
        $destinationUrl = Url::fromString('http://foo-bar.com');

        $destinationUrlRepository->getDestinationUrl()->willReturn($destinationUrl);

        $authService = $this->prophesize(AuthService::class);
        $authService->token()->willReturn(null);

        $generateDestinationUrl = $this->prophesize(GenerateAuthorizedDestinationUrl::class);

        $authorizeAction = new Authorize(
            $authService->reveal(),
            $destinationUrlRepository->reveal(),
            $generateDestinationUrl->reveal()
        );

        $this->expectException(NoTokenPresent::class);
        $authorizeAction->__invoke();
    }

}
