<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Auth0\SDK\Contract\API\AuthenticationInterface;
use Auth0\SDK\Contract\Auth0Interface;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Slim\Psr7\Factory\UriFactory;

final class LogOutOAuthAdapterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_logs_out_user(): void
    {
        $auth0 = $this->prophesize(Auth0Interface::class);
        $authentication = $this->prophesize(AuthenticationInterface::class);
        $auth0LogOutUri = 'https://auth0/logout?destinationTo=http://foo-bar.com';
        $auth0->logout()->willReturn('http://foo-bar.com');

        $authentication->getLogoutLink('http://foo-bar.com', ['clientId' => 'client-id'])->willReturn($auth0LogOutUri);

        $logOutOAuthAdapter = new LogOutOAuthAdapter(
            $auth0->reveal(),
            $authentication->reveal(),
            new SlimResponseFactory(),
            new UriFactory(),
            'http://foo-bar.com',
            'client-id'
        );

        $response = $logOutOAuthAdapter->logout();

        $this->assertEquals(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
        $this->assertEquals($auth0LogOutUri, $response->getHeaderLine('Location'));
        $auth0->logout()->shouldHaveBeenCalled();
    }
}
