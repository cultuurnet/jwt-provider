<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractDestinationUrlFromRequest;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LogOutServiceInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Response;

class RequestLogoutTest extends TestCase
{

    /**
     * @test
     */
    public function it_logs_out_user()
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $destinationUrl = (new UriFactory())->createUri('http://foo-bar.com/');

        /** @var ExtractDestinationUrlFromRequest|ObjectProphecy $extractDestinationUrlFromRequest */
        $extractDestinationUrlFromRequest = $this->prophesize(ExtractDestinationUrlFromRequest::class);
        $extractDestinationUrlFromRequest->__invoke($serverRequest)->willReturn($destinationUrl);

        /** @var LogOutServiceInterface|OAuth0AdapterTestbjectProphecy $authService */
        $authService = $this->prophesize(LogOutServiceInterface::class);
        $expectedResponse = new Response();
        $authService->logout()->willReturn($expectedResponse);

        /** @var DestinationUrlRepositoryInterface|ObjectProphecy $repository */
        $repository = $this->prophesize(DestinationUrlRepositoryInterface::class);

        $logoutAction = new RequestLogout(
            $extractDestinationUrlFromRequest->reveal(),
            $authService->reveal(),
            $repository->reveal()
        );

        $response = $logoutAction->__invoke(
            $serverRequest->reveal()
        );

        $this->assertEquals($expectedResponse, $response);
        $repository->storeDestinationUrl($destinationUrl)->shouldHaveBeenCalled();
    }
}
