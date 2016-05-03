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
    private $oauthService;

    /**
     * @var RequestTokenStorageInterface
     */
    private $requestTokenStorage;

    /**
     * @var OAuthUrlHelper
     */
    private $oauthUrlHelper;

    /**
     * @var OAuthCallbackHandlerInterface
     */
    private $oauthCallbackHandler;

    /**
     * @var StringLiteral
     */
    private $defaultDestination;

    public function __construct(
        OAuthServiceInterface $oauthService,
        RequestTokenStorageInterface $requestTokenStorage,
        OAuthUrlHelper $oauthUrlHelper,
        OAuthCallbackHandlerInterface $oauthCallbackHandler,
        StringLiteral $defaultDestination = null
    ) {
        $this->oauthService = $oauthService;
        $this->requestTokenStorage = $requestTokenStorage;
        $this->oauthUrlHelper = $oauthUrlHelper;
        $this->oauthCallbackHandler = $oauthCallbackHandler;

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
        $callbackUrl = $this->oauthUrlHelper->createCallbackUrl($request);

        $requestToken = $this->oauthService->getRequestToken($callbackUrl);
        $this->requestTokenStorage->storeRequestToken($requestToken);

        $authorizeUrl = $this->oauthService->getAuthorizeUrl($requestToken);
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

        $uri = $this->oauthUrlHelper->createDestinationUri();

        return $this->oauthCallbackHandler->handle($accessToken, $uri);
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

        if ($this->oauthUrlHelper->hasValidAccessToken($request, $requestToken)) {
            $accessToken = $this->oauthService->getAccessToken(
                $requestToken,
                $request->query->get(OAuthUrlHelper::OAUTH_VERIFIER)
            );
        }

        return $accessToken;
    }
}
