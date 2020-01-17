<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthServiceInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractDestinationUrlFromRequest;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\UriFactory;

class LogoutTest extends TestCase
{

    /**
     * @test
     */
    public function it_logs_out_user()
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $destinationUrl = (new UriFactory())->createUri('http://foo-bar.com/');

        $extractDestinationUrlFromRequest = $this->prophesize(ExtractDestinationUrlFromRequest::class);
        $extractDestinationUrlFromRequest->__invoke($serverRequest)->willReturn($destinationUrl);

        $authService = $this->prophesize(AuthServiceInterface::class);

        $logoutAction = new Logout(
            $extractDestinationUrlFromRequest->reveal(),
            $authService->reveal(),
            new SlimResponseFactory()
        );

        $response = $logoutAction->__invoke(
            $serverRequest->reveal()
        );

        $this->assertEquals('http://foo-bar.com/', $response->getHeaderLine('Location'));
        $this->assertEquals(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
        $authService->logout()->shouldHaveBeenCalled();
    }
}
