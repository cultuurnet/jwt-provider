<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\ClientInformationNotPresentException;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ClientInformationRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Value\ClientInformation;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Slim\Psr7\Factory\UriFactory;

final class LogOutTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_redirects_user_back_to_destination(): void
    {
        $clientInformation = $this->aClientInformation();
        $clientInformationRepository = $this->prophesize(ClientInformationRepositoryInterface::class);
        $clientInformationRepository->get()->willReturn($clientInformation);

        $logOutAction = new LogOut(
            $clientInformationRepository->reveal(),
            new SlimResponseFactory()
        );
        $response = $logOutAction->__invoke();

        $this->assertEquals('http://foo-bar.com', $response->getHeaderLine('Location'));
    }

    /**
     * @test
     */
    public function it_throws_exception_for_no_client_information(): void
    {
        $clientInformationRepository = $this->prophesize(ClientInformationRepositoryInterface::class);
        $clientInformationRepository->get()->willReturn(null);


        $logOutAction = new LogOut(
            $clientInformationRepository->reveal(),
            new SlimResponseFactory()
        );

        $this->expectException(ClientInformationNotPresentException::class);
        $logOutAction->__invoke();
    }

    private function aClientInformation(): ClientInformation
    {
        return new ClientInformation(
            (new UriFactory())->createUri('http://foo-bar.com'),
            new ApiKey('api-key')
        );
    }
}
