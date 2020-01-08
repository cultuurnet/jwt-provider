<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Unit\Infrastructure\Service;

use Auth0\SDK\Auth0;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\Auth0Adapter;
use PHPUnit\Framework\TestCase;

class Auth0AdapterTest extends TestCase
{

    /**
     * @test
     */
    public function it_redirects_to_login_page()
    {
        $auth0 = $this->prophesize(Auth0::class);

        $auth0adapter = new Auth0Adapter($auth0->reveal());

        $auth0->login()->shouldBeCalled();

        $auth0adapter->redirectToLogin();

    }
}
