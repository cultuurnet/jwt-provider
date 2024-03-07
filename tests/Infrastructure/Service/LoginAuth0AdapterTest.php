<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Auth0\SDK\Auth0;
use Auth0\SDK\Exception\ApiException;
use Auth0\SDK\Exception\CoreException;
use CultuurNet\UDB3\JwtProvider\Domain\Enum\Locale;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class LoginAuth0AdapterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_redirects_to_login_page(): void
    {
        $auth0 = $this->prophesize(Auth0::class);

        $auth0adapter = new LoginAuth0Adapter(
            $auth0->reveal()
        );

        $auth0->login(
            null,
            null,
            [
                'locale' => Locale::DUTCH,
                'referrer' => 'udb',
                'skip_verify_legacy' => 'true',
                'product_display_name' => 'UiTdatabank',
            ]
        )->shouldBeCalled();

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
     */
    public function it_returns_refresh_token(): void
    {
        $auth0 = $this->prophesize(Auth0::class);

        $auth0adapter = new LoginAuth0Adapter(
            $auth0->reveal()
        );

        $auth0->getRefreshToken()->willReturn('refresh-token');

        $this->assertEquals('refresh-token', $auth0adapter->refreshToken());
    }

    /**
     * @test
     * @dataProvider auth0_exceptions()
     * @throws ApiException
     * @throws CoreException
     * @throws UnSuccessfulAuthException
     */
    public function it_wraps_auth0_exceptions_to_unsuccessful_auth_exception(Exception $exceptionClass): void
    {
        $auth0 = $this->prophesize(Auth0::class);

        $auth0adapter = new LoginAuth0Adapter(
            $auth0->reveal()
        );

        $auth0->getIdToken()->willThrow($exceptionClass);

        $this->expectException(UnSuccessfulAuthException::class);

        $auth0adapter->token();
    }

    /**
     * @return Exception[][]
     */
    public function auth0_exceptions(): array
    {
        return [
            [new ApiException()],
            [new CoreException()],
        ];
    }
}
