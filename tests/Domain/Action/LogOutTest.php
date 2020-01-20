<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Slim\Psr7\Factory\UriFactory;

class LogOutTest extends TestCase
{

    /**
     * @test
     */
    public function it_redirects_user_back_to_destination()
    {
        $destinationUrl = (new UriFactory())->createUri('http://foo-bar.com');
        /** @var DestinationUrlRepositoryInterface|ObjectProphecy $repository */
        $repository = $this->prophesize(DestinationUrlRepositoryInterface::class);

        $repository->getDestinationUrl()->willReturn($destinationUrl);

        $logOutAction = new LogOut(
            $repository->reveal(),
            new SlimResponseFactory()
        );
        $response = $logOutAction->__invoke();

        $this->assertEquals('http://foo-bar.com/', $response->getHeaderLine('Location'));
    }


    /**
     * @test
     */
    public function it_returns_bad_request_if_destination_url_not_present()
    {
        /** @var DestinationUrlRepositoryInterface|ObjectProphecy $repository */
        $repository = $this->prophesize(DestinationUrlRepositoryInterface::class);

        $repository->getDestinationUrl()->willReturn(null);

        $logOutAction = new LogOut(
            $repository->reveal(),
            new SlimResponseFactory()
        );
        $response = $logOutAction->__invoke();

        $this->assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }
}
