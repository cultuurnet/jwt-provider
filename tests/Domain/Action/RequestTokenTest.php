<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Repository\ClientInformationRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LoginServiceInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Value\ClientInformation;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\ExtractClientInformationFromRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\UriFactory;
use ValueObjects\StringLiteral\StringLiteral;

class RequestTokenTest extends TestCase
{

    /**
     * @test
     */
    public function it_requests_for_token(): void
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);

        $clientInformation = $this->aClientInformation();

        $extractClientInformationFromRequest = $this->prophesize(ExtractClientInformationFromRequest::class);
        $extractClientInformationFromRequest->__invoke($serverRequest)->willReturn($clientInformation);

        $externalAuthService = $this->prophesize(LoginServiceInterface::class);
        $externalAuthService->redirectToLogin()->shouldBeCalled();

        $clientInformationRepository = $this->prophesize(ClientInformationRepositoryInterface::class);
        $clientInformationRepository->store($clientInformation)->shouldBeCalled();

        $requestTokenAction = new RequestToken(
            $extractClientInformationFromRequest->reveal(),
            $externalAuthService->reveal(),
            new SlimResponseFactory(),
            $clientInformationRepository->reveal()
        );

        $requestTokenAction->__invoke($serverRequest->reveal());
    }

    private function aClientInformation() : ClientInformation
    {
        return new ClientInformation(
            (new UriFactory())->createUri('http://foo-bar.com'),
            new StringLiteral('api-key')
        );
    }
}
