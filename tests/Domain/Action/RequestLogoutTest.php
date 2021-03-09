<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ClientInformationRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractClientInformationFromRequestInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LogOutServiceInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Value\ClientInformation;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Response;

final class RequestLogoutTest extends TestCase
{
    /**
     * @test
     */
    public function it_logs_out_user(): void
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);

        $clientInformation = $this->aClientInformation();
        $extractClientInformationFromRequest = $this->prophesize(ExtractClientInformationFromRequestInterface::class);
        $extractClientInformationFromRequest->__invoke($serverRequest)->willReturn($clientInformation);

        $clientInformationRepository = $this->prophesize(ClientInformationRepositoryInterface::class);

        $expectedResponse = new Response();
        $authService = $this->prophesize(LogOutServiceInterface::class);
        $authService->logout()->willReturn($expectedResponse);


        $logoutAction = new RequestLogout(
            $extractClientInformationFromRequest->reveal(),
            $authService->reveal(),
            $clientInformationRepository->reveal()
        );

        $response = $logoutAction->__invoke(
            $serverRequest->reveal()
        );

        $this->assertEquals($expectedResponse, $response);

        $clientInformationRepository->store($clientInformation)->shouldHaveBeenCalled();
    }

    private function aClientInformation(): ClientInformation
    {
        return new ClientInformation(
            (new UriFactory())->createUri('http://foo-bar.com'),
            new ApiKey('api-key')
        );
    }
}
