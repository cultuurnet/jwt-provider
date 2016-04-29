<?php
namespace CultuurNet\UDB3\JwtProvider\OAuth;

use Silex\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use ValueObjects\String\String as StringLiteral;
use CultuurNet\Auth\TokenCredentials as RequestToken;

class OAuthUrlHelperTest extends \PHPUnit_Framework_TestCase
{
    const TEST_TOKEN = 'testToken';
    const TEST_SECRET = 'testSecret';
    const TEST_VERIFIER = 'testVerifier';
    const DEFAULT_DESTINATION = 'http://www.default.com';

    /**
     * @var OAuthUrlHelper
     */
    private $oAuthUrlHelper;
    
    /**
     * @var StringLiteral
     */
    private $defaultDestination;
    
    /**
     * @var RequestToken
     */
    private $requestToken;
    
    public function setUp()
    {
        $this->requestToken = new RequestToken(
            self::TEST_TOKEN,
            self::TEST_SECRET
        );
        
        $this->oAuthUrlHelper = new OAuthUrlHelper($this->createUrlGenerator());

        $this->defaultDestination = new StringLiteral(self::DEFAULT_DESTINATION);
    }
    
    /**
     * @test
     */
    public function it_creates_callback_url_from_request()
    {
        $request = Request::create(
            'http://culudb-jwt-provider.dev/culturefeed/oauth/authorize',
            'GET',
            ['destination' => 'culudb-silex.dev']
        );
        $expectedUrl = '//culturefeed/oauth/authorize%3Fdestination=culudb-silex.dev';

        $callbackUrl = $this->oAuthUrlHelper->createCallbackUrl($request);

        $this->assertEquals($expectedUrl, $callbackUrl->toNative());
    }

    /**
     * @test
     */
    public function it_returns_null_for_callback_url_when_destination_is_missing()
    {
        $request = Request::create(
            '/culturefeed/oauth/authorize',
            'GET'
        );

        $callbackUrl = $this->oAuthUrlHelper->createCallbackUrl($request);
        
        $this->assertNull($callbackUrl);
    }
    
    /**
     * @test
     */
    public function access_token_is_valid_when_token_match_and_verifier_is_present()
    {
        $url = $this->createUrlWithTokenAndVerifier(
            self::TEST_TOKEN,
            self::TEST_VERIFIER
        );

        $request = Request::create($url);

        $hasValidAccessToken = $this->oAuthUrlHelper->hasValidAccessToken(
            $request,
            $this->requestToken
        );

        $this->assertTrue($hasValidAccessToken);
    }

    /**
     * @test
     */
    public function access_token_is_not_valid_when_token_does_not_match()
    {
        $url = $this->createUrlWithTokenAndVerifier(
            'wrongToken',
            self::TEST_VERIFIER
        );

        $request = Request::create($url);

        $hasValidAccessToken = $this->oAuthUrlHelper->hasValidAccessToken(
            $request,
            $this->requestToken
        );

        $this->assertFalse($hasValidAccessToken);
    }

    /**
     * @test
     */
    public function access_token_is_not_valid_when_verifier_is_missing()
    {
        $url = $this->createUrlWithTokenAndVerifier(
            self::TEST_TOKEN,
            ''
        );

        $request = Request::create($url);

        $hasValidAccessToken = $this->oAuthUrlHelper->hasValidAccessToken(
            $request,
            $this->requestToken
        );

        $this->assertFalse($hasValidAccessToken);
    }

    /**
     * @param string $token
     * @param string $verifier
     * @return string
     */
    private function createUrlWithTokenAndVerifier(
        $token,
        $verifier
    ) {
        $baseUrl = 'http://www.example.com?';

        if (!empty($token)) {
            $baseUrl .= 'oauth_token=' . $token . '&';
        }

        if (!empty($verifier)) {
            $baseUrl .= 'oauth_verifier=' . $verifier;
        }

        return $baseUrl;
    }

    /**
     * @return UrlGenerator
     */
    private function createUrlGenerator()
    {
        $routes = new RouteCollection();
        $routes->add(
            OAuthUrlHelper::AUTHORISATION_ROUTE_NAME,
            new Route('/culturefeed/oauth/authorize?destination={destination}')
        );

        $context = new RequestContext('/');

        return new UrlGenerator($routes, $context);
    }
}
