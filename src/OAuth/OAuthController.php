<?php
namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\Auth\ServiceInterface as OAuthServiceInterface;
use CultuurNet\Auth\TokenCredentials as RequestToken;
use CultuurNet\Auth\User as AccessToken;
use CultuurNet\UDB3\JwtProvider\RequestTokenStorage\RequestTokenStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\String\String as StringLiteral;

class OAuthController
{
    /**
     * @var OAuthServiceInterface
     */
    private $oAuthService;

    /**
     * @var RequestTokenStorageInterface
     */
    private $requestTokenStorage;

    /**
     * @var OAuthUrlHelper
     */
    private $oAuthUrlHelper;

    /**
     * @var OAuthResponseFactoryInterface
     */
    private $oAuthResponseFactory;

    /**
     * @var StringLiteral
     */
    private $defaultDestination;

    public function __construct(
        OAuthServiceInterface $oAuthService,
        RequestTokenStorageInterface $requestTokenStorage,
        OAuthUrlHelper $oAuthUrlHelper,
        OAuthResponseFactoryInterface $oAuthResponseFactory,
        StringLiteral $defaultDestination = null
    ) {
        $this->oAuthService = $oAuthService;

        $this->requestTokenStorage = $requestTokenStorage;

        $this->oAuthUrlHelper = $oAuthUrlHelper;

        $this->oAuthResponseFactory = $oAuthResponseFactory;

        if ($defaultDestination === null) {
            $defaultDestination = new StringLiteral('/');
        }
        $this->defaultDestination = $defaultDestination;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function connect(Request $request)
    {
        $callbackUrl = $this->oAuthUrlHelper->createCallbackUrl($request);

        $requestToken = $this->oAuthService->getRequestToken($callbackUrl);
        $this->requestTokenStorage->storeRequestToken($requestToken);

        $authorizeUrl = $this->oAuthService->getAuthorizeUrl($requestToken);
        return new RedirectResponse($authorizeUrl);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function authorize(Request $request)
    {
        $requestToken = $this->requestTokenStorage->getStoredRequestToken();

        $accessToken = $this->getAccessToken($request, $requestToken);

        if ($accessToken) {
            $this->requestTokenStorage->removeStoredRequestToken();
        }

        $uri = $this->oAuthUrlHelper->createDestinationUri();

        return $this->oAuthResponseFactory->create($accessToken, $uri);
    }

    /**
     * @param Request $request
     * @param RequestToken $requestToken
     * @return AccessToken|null
     */
    private function getAccessToken(
        Request $request,
        RequestToken $requestToken
    ) {
        $accessToken = null;

        if ($this->oAuthUrlHelper->hasValidAccessToken($request, $requestToken)) {
            $accessToken = $this->oAuthService->getAccessToken(
                $requestToken,
                $request->query->get(OAuthUrlHelper::OAUTH_VERIFIER)
            );
        }

        return $accessToken;
    }
}
