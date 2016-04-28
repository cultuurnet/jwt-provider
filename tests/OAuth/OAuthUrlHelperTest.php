<?php
namespace CultuurNet\UDB3\JwtProvider\OAuth;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use ValueObjects\String\String as StringLiteral;
use CultuurNet\Auth\TokenCredentials as RequestToken;

class OAuthUrlHelperTest extends \PHPUnit_Framework_TestCase
{
    /** @var OAuthUrlHelper */
    private $oauthUrlHelper;
    
    /** @var UrlGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $urlGenerator;
    
    /** @var Request|\PHPUnit_Framework_MockObject_MockObject */
    private $request;
    
    /** @var ParameterBag|\PHPUnit_Framework_MockObject_MockObject */
    private $parameterBag;
    
    /** @var StringLiteral */
    private $defaultDestination;
    
    /** @var RequestToken */
    private $requestToken;
    private $requestTokenToken = 'testToken';
    private $requestTokenSecret = 'testSecret';
    
    public function setUp()
    {
        /*
         * TODO: would it be interesting to not mock urlGenerator, but have an implementation
        */
        $this->urlGenerator = $this->getMock(UrlGeneratorInterface::class);
        $this->requestToken = new RequestToken($this->requestTokenToken, $this->requestTokenSecret);
        $this->oauthUrlHelper = new OAuthUrlHelper($this->urlGenerator);
        $this->defaultDestination = new StringLiteral("http://defaultDestination");
    }
    
    /**
     * @test
     */
    public function it_should_return_default_redirect_for_successful_when_creating_authorization_response()
    {
        $redirectedTarget = 'http://redirect.example.com';
        $this->request = Request::create('http://example.com/?test');
        $this->urlGenerator->expects($this->once())
            ->method('generate')->willReturn($redirectedTarget);
        $this->oauthUrlHelper->createAuthorizationResponse($this->request, $this->defaultDestination);
    }
    
    /**
     * @test
     */
    public function it_should_return_passed_redirect_for_successful_when_creating_authorization_response()
    {
        $this->request = Request::create('http://example.com/?destination=test');
        $this->urlGenerator->expects($this->never())->method('generate');
        $redirect = $this->oauthUrlHelper->createAuthorizationResponse($this->request, $this->defaultDestination);
        $this->assertEquals('test', $redirect->getTargetUrl());
    }

    /**
     * @test
     */
    public function it_should_create_a_callbackUrl_with_request_parameter()
    {
        $redirectedTarget = 'http://redirect.example.com';
        $this->request = Request::create('http://example.com/?destination=test');
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                OAuthUrlHelper::AUTHORISATION_ROUTE_NAME,
                ['test'],
                UrlGeneratorInterface::ABSOLUTE_PATH
            )
            ->willReturn($redirectedTarget);
        $this->oauthUrlHelper->createCallbackUrl($this->request);
    }
    
    /**
     * @test
     */
    public function it_should_validate_tokens()
    {
        $redirectedTarget = 'http://redirect.example.com';
        $this->request = Request::create('http://example.com/?oauth_verifier&oauth_token='.$this->requestTokenToken);
        $hasAccessToken = $this->oauthUrlHelper->hasAccessToken($this->request, $this->requestToken);
        echo $hasAccessToken;
    }
}
