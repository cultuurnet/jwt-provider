<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Auth0\SDK\API\Authentication;
use Auth0\SDK\Auth0;
use Auth0\SDK\Exception\ApiException;
use Auth0\SDK\Exception\CoreException;
use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ApiKeyRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\IsAllowedRefreshToken;
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
        $apiKeyRepository = $this->prophesize(ApiKeyRepositoryInterface::class);
        $isAllowedRefreshToken = $this->prophesize(IsAllowedRefreshToken::class);

        $auth0adapter = new LoginAuth0Adapter(
            $auth0->reveal(),
            $apiKeyRepository->reveal(),
            $isAllowedRefreshToken->reveal()
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
        $apiKeyRepository = $this->prophesize(ApiKeyRepositoryInterface::class);
        $isAllowedRefreshToken = $this->prophesize(IsAllowedRefreshToken::class);

        $auth0adapter = new LoginAuth0Adapter(
            $auth0->reveal(),
            $apiKeyRepository->reveal(),
            $isAllowedRefreshToken->reveal()
        );

        $auth0->getIdToken()->willReturn('token');

        $this->assertEquals('token', $auth0adapter->token());
    }

    /**
     * @test
     */
    public function it_returns_refresh_token_if_api_key_that_has_appropriate_permission()
    {
        $apiKey = new ApiKey('key');
        $auth0 = $this->prophesize(Auth0::class);
        $apiKeyRepository = $this->prophesize(ApiKeyRepositoryInterface::class);
        $isAllowedRefreshToken = $this->prophesize(IsAllowedRefreshToken::class);

        $apiKeyRepository->getApiKey()->willReturn($apiKey);
        $isAllowedRefreshToken->__invoke($apiKey)->willReturn(true);

        $auth0adapter = new LoginAuth0Adapter(
            $auth0->reveal(),
            $apiKeyRepository->reveal(),
            $isAllowedRefreshToken->reveal()
        );

        $auth0->getRefreshToken()->willReturn('refresh-token');

        $this->assertEquals('refresh-token', $auth0adapter->refreshToken());
    }

    /**
     * @test
     */
    public function it_returns_null_refresh_token_if_api_key_does_not_have_appropriate_permission()
    {
        $apiKey = new ApiKey('key');
        $auth0 = $this->prophesize(Auth0::class);
        $apiKeyRepository = $this->prophesize(ApiKeyRepositoryInterface::class);
        $isAllowedRefreshToken = $this->prophesize(IsAllowedRefreshToken::class);

        $apiKeyRepository->getApiKey()->willReturn($apiKey);
        $isAllowedRefreshToken->__invoke($apiKey)->willReturn(false);

        $auth0adapter = new LoginAuth0Adapter(
            $auth0->reveal(),
            $apiKeyRepository->reveal(),
            $isAllowedRefreshToken->reveal()
        );

        $auth0->getRefreshToken()->shouldNotBeCalled();

        $this->assertEquals(null, $auth0adapter->refreshToken());
    }

    /**
     * @test
     * @dataProvider auth0_exceptions()
     * @param string $exceptionClassName
     * @throws ApiException
     * @throws CoreException
     * @throws UnSuccessfulAuthException
     */
    public function it_wraps_auth0_exceptions_to_unsuccessful_auth_exception(string $exceptionClassName): void
    {

        $auth0 = $this->prophesize(Auth0::class);
        $apiKeyRepository = $this->prophesize(ApiKeyRepositoryInterface::class);
        $isAllowedRefreshToken = $this->prophesize(IsAllowedRefreshToken::class);

        $auth0adapter = new LoginAuth0Adapter(
            $auth0->reveal(),
            $apiKeyRepository->reveal(),
            $isAllowedRefreshToken->reveal()
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
