<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\JwtProvider\Domain\Enum\Locale;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ClientInformationRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractClientInformationFromRequestInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LoginServiceInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Value\ClientInformation;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\ExtractLocaleFromRequest;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\UriFactory;

final class RequestTokenTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_requests_for_token(): void
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $serverRequest->getQueryParams()->willReturn(['lang' => Locale::FRENCH]);

        $clientInformation = $this->aClientInformation();

        $extractClientInformationFromRequest = $this->prophesize(ExtractClientInformationFromRequestInterface::class);
        $extractClientInformationFromRequest->__invoke($serverRequest)->willReturn($clientInformation);

        $clientInformationRepository = $this->prophesize(ClientInformationRepositoryInterface::class);
        $clientInformationRepository->store($clientInformation)->shouldBeCalled();

        $extractLocaleFromRequest = new ExtractLocaleFromRequest();

        $externalAuthService = $this->prophesize(LoginServiceInterface::class);
        $externalAuthService->redirectToLogin('fr')->shouldBeCalled();

        $requestTokenAction = new RequestToken(
            $extractClientInformationFromRequest->reveal(),
            $externalAuthService->reveal(),
            $clientInformationRepository->reveal(),
            $extractLocaleFromRequest
        );

        $requestTokenAction->__invoke($serverRequest->reveal());
    }

    private function aClientInformation(): ClientInformation
    {
        return new ClientInformation(
            (new UriFactory())->createUri('http://foo-bar.com'),
            new ApiKey('api-key')
        );
    }
}
