<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ClientInformationRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\GenerateAuthorizedDestinationUrl;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LoginServiceInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Value\ClientInformation;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Slim\Psr7\Factory\UriFactory;

final class AuthorizeTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_returns_response_with_authorized_url_for_successful_authorization(): void
    {
        $authService = $this->prophesize(LoginServiceInterface::class);
        $authService->token()->willReturn('token');
        $authService->refreshToken()->willReturn('refresh');

        $clientInformation = $this->aClientInformation();
        $clientInformationRepository = $this->prophesize(ClientInformationRepositoryInterface::class);
        $clientInformationRepository->get()->willReturn($clientInformation);

        $authorizeAction = new Authorize(
            $authService->reveal(),
            new GenerateAuthorizedDestinationUrl(),
            new SlimResponseFactory(),
            $clientInformationRepository->reveal()
        );

        $response = $authorizeAction->__invoke();

        $this->assertEquals('http://foo-bar.com?jwt=token&refresh=refresh', $response->getHeaderLine('Location'));
    }

    private function aClientInformation(): ClientInformation
    {
        return new ClientInformation(
            (new UriFactory())->createUri('http://foo-bar.com'),
            new ApiKey('api-key'),
            true
        );
    }
}
