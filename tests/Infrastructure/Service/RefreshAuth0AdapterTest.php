<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Auth0\SDK\Auth0;
use Auth0\SDK\Exception\ApiException;
use Auth0\SDK\Exception\CoreException;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;
use PHPUnit\Framework\TestCase;

class RefreshAuth0AdapterTest extends TestCase
{

    /**
     * @test
     */
    public function it_refreshes_token()
    {
        $auth0 = $this->prophesize(Auth0::class);
        $auth0adapter = new RefreshAuth0Adapter($auth0->reveal());
        $auth0adapter->renewToken();
        $auth0->renewTokens()->shouldHaveBeenCalled();
    }

    /**
     * @test
     * @dataProvider auth0_exceptions()
     * @param string $exceptionClassName
     * @throws ApiException
     * @throws CoreException
     * @throws UnSuccessfulAuthException
     */
    public function it_wraps_auth0_exceptions_to_unsuccessful_auth_exception_during_token_refresh(string $exceptionClassName): void
    {

        $auth0 = $this->prophesize(Auth0::class);

        $auth0adapter = new RefreshAuth0Adapter($auth0->reveal());

        $auth0->renewTokens()->willThrow($exceptionClassName);

        $this->expectException(UnSuccessfulAuthException::class);

        $auth0adapter->renewToken();
    }

    public function auth0_exceptions()
    {
        return [
            [ApiException::class],
            [CoreException::class],
        ];
    }
}
