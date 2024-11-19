<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Auth0\SDK\Auth0;
use Auth0\SDK\Contract\Auth0Interface;
use Auth0\SDK\Exception\NetworkException;
use Auth0\SDK\Exception\StateException;
use CultuurNet\UDB3\JwtProvider\Domain\Enum\Locale;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class LoginOAuthAdapterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_redirects_to_login_page(): void
    {
        $auth0 = $this->prophesize(Auth0Interface::class);

        $loginOAuthAdapter = new LoginOAuthAdapter(
            $auth0->reveal()
        );

        $auth0->login(
            null,
            [
                'locale' => Locale::DUTCH,
                'referrer' => 'udb',
                'skip_verify_legacy' => 'true',
                'product_display_name' => 'UiTdatabank',
            ]
        )->shouldBeCalled();

        $loginOAuthAdapter->redirectToLogin();
    }

    /**
     * @test
     */
    public function it_returns_token(): void
    {
        $auth0 = $this->prophesize(Auth0Interface::class);

        $loginOAuthAdapter = new LoginOAuthAdapter(
            $auth0->reveal()
        );

        $auth0->getIdToken()->willReturn('token');
        $auth0->exchange()->willReturn(true);

        $this->assertEquals('token', $loginOAuthAdapter->token());
    }

    /**
     * @test
     */
    public function it_returns_refresh_token(): void
    {
        $auth0 = $this->prophesize(Auth0Interface::class);

        $loginOAuthAdapter = new LoginOAuthAdapter(
            $auth0->reveal()
        );

        $auth0->getRefreshToken()->willReturn('refresh-token');

        $this->assertEquals('refresh-token', $loginOAuthAdapter->refreshToken());
    }

    /**
     * @test
     * @dataProvider auth0_exceptions()
     * @throws UnSuccessfulAuthException
     */
    public function it_wraps_auth0_exceptions_to_unsuccessful_auth_exception(Exception $exceptionClass): void
    {
        $auth0 = $this->prophesize(Auth0Interface::class);

        $loginOAuthAdapter = new LoginOAuthAdapter(
            $auth0->reveal()
        );

        $auth0->getIdToken()->willThrow($exceptionClass);
        $auth0->exchange()->willReturn(true);

        $this->expectException(UnSuccessfulAuthException::class);

        $loginOAuthAdapter->token();
    }

    /**
     * @return Exception[][]
     */
    public function auth0_exceptions(): array
    {
        return [
            [new NetworkException()],
            [new StateException()],
        ];
    }
}
