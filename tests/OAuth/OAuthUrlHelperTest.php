<?php
namespace CultuurNet\UDB3\JwtProvider\OAuth;

use InvalidArgumentException;
use CultuurNet\Auth\TokenCredentials as RequestToken;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;

class OAuthUrlHelperTest extends TestCase
{
    public const TEST_TOKEN = 'testToken';
    public const TEST_SECRET = 'testSecret';
    public const TEST_VERIFIER = 'testVerifier';
    public const DEFAULT_DESTINATION = 'http://www.default.com';

    private OAuthUrlHelper $oAuthUrlHelper;

    private RequestToken $requestToken;

    public function setUp(): void
    {
        $this->requestToken = new RequestToken(
            self::TEST_TOKEN,
            self::TEST_SECRET
        );

        $this->oAuthUrlHelper = new OAuthUrlHelper('/authorize');
    }

    /**
     * @test
     */
    public function it_creates_callback_uri_from_request(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'http://culudb-jwt-provider.dev/connect?destination=http://culudb-silex.dev'
        );
        $expectedUri = 'http://culudb-jwt-provider.dev/authorize?destination=' . urlencode('http://culudb-silex.dev');

        $callbackUri = $this->oAuthUrlHelper->createCallbackUri($request);

        $this->assertEquals($expectedUri, (string) $callbackUri);
    }

    /**
     * @test
     */
    public function it_throws_invalid_argument_exception_when_destination_is_missing_in_request(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            '/connect'
        );

        $this->expectException(InvalidArgumentException::class);

        $callbackUrl = $this->oAuthUrlHelper->createCallbackUri($request);

        $this->assertNull($callbackUrl);
    }

    /**
     * @test
     */
    public function request_token_is_valid_when_token_match_and_verifier_is_present(): void
    {
        $url = $this->createUrlWithTokenAndVerifier(
            self::TEST_TOKEN,
            self::TEST_VERIFIER
        );

        $request = (new ServerRequestFactory())->createServerRequest('GET', $url);

        $hasValidRequestToken = $this->oAuthUrlHelper->hasValidRequestToken(
            $request,
            $this->requestToken
        );

        $this->assertTrue($hasValidRequestToken);
    }

    /**
     * @test
     */
    public function request_token_is_not_valid_when_token_does_not_match(): void
    {
        $url = $this->createUrlWithTokenAndVerifier(
            'wrongToken',
            self::TEST_VERIFIER
        );

        $request = (new ServerRequestFactory())->createServerRequest('GET', $url);

        $hasValidRequestToken = $this->oAuthUrlHelper->hasValidRequestToken(
            $request,
            $this->requestToken
        );

        $this->assertFalse($hasValidRequestToken);
    }

    /**
     * @test
     */
    public function request_token_is_not_valid_when_verifier_is_missing(): void
    {
        $url = $this->createUrlWithTokenAndVerifier(
            self::TEST_TOKEN,
            ''
        );

        $request = (new ServerRequestFactory())->createServerRequest('GET', $url);

        $hasValidRequestToken = $this->oAuthUrlHelper->hasValidRequestToken(
            $request,
            $this->requestToken
        );

        $this->assertFalse($hasValidRequestToken);
    }

    private function createUrlWithTokenAndVerifier(
        string $token,
        string $verifier
    ): string {
        $baseUrl = 'http://www.example.com?';

        if (!empty($token)) {
            $baseUrl .= 'oauth_token=' . $token . '&';
        }

        if (!empty($verifier)) {
            $baseUrl .= 'oauth_verifier=' . $verifier;
        }

        return $baseUrl;
    }
}
