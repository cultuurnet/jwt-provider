<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Auth0\SDK\API\Authentication;
use Auth0\SDK\Auth0;
use Auth0\SDK\Exception\ApiException;
use Auth0\SDK\Exception\CoreException;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\UriFactory;

class LoginAuth0AdapterTest extends TestCase
{
    /**
     * @test
     */
    public function it_redirects_to_login_page(): void
    {
        $auth0 = $this->prophesize(Auth0::class);

        $auth0adapter = new LoginAuth0Adapter(
            $auth0->reveal()
        );

        $auth0->login()->shouldBeCalled();

        $auth0adapter->redirectToLogin();
    }

    /**
     * @test
     */
    public function it_returns_token(): void
    {
        $auth0 = $this->prophesize(Auth0::class);

        $auth0adapter = new LoginAuth0Adapter(
            $auth0->reveal()
        );

        $auth0->getIdToken()->willReturn('token');

        $this->assertEquals('token', $auth0adapter->token());
    }


    /**
     * @test
     * @dataProvider auth0_exceptions()
     * @param string $exceptionClassName
     * @throws UnSuccessfulAuthException
     */
    public function it_wraps_auth0_exceptions_to_unsuccessful_auth_exception(string $exceptionClassName): void
    {

        $auth0 = $this->prophesize(Auth0::class);

        $auth0adapter = new LoginAuth0Adapter(
            $auth0->reveal()
        );

        $auth0->getIdToken()->willThrow($exceptionClassName);

        $this->expectException(UnSuccessfulAuthException::class);

        $auth0adapter->token();

    }

    public function auth0_exceptions()
    {
        return [
            [ApiException::class],
            [CoreException::class],
        ];
    }
}
