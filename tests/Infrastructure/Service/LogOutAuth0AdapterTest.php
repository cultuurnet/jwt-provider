<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Auth0\SDK\API\Authentication;
use Auth0\SDK\Auth0;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\UriFactory;

final class LogOutAuth0AdapterTest extends TestCase
{
    /**
     * @test
     */
    public function it_logs_out_user()
    {
        $auth0 = $this->prophesize(Auth0::class);
        $authentication = $this->prophesize(Authentication::class);
        $auth0LogOutUri = 'https://auth0/logout?destinationTo=http://foo-bar.com/';

        $authentication->get_logout_link('http://foo-bar.com/', 'client-id')->willReturn($auth0LogOutUri);

        $auth0adapter = new LogOutAuth0Adapter(
            $auth0->reveal(),
            $authentication->reveal(),
            new SlimResponseFactory(),
            new UriFactory(),
            'http://foo-bar.com/',
            'client-id'
        );

        $response = $auth0adapter->logout();

        $this->assertEquals(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
        $this->assertEquals($auth0LogOutUri, $response->getHeaderLine('Location'));
        $auth0->logout()->shouldHaveBeenCalled();
    }
}
