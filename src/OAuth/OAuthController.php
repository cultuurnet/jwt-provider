<?php
namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\Auth\ServiceInterface as OAuthServiceInterface;
use CultuurNet\UDB3\JwtProvider\RequestTokenStorage\RequestTokenStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @var OAuthCallbackHandlerInterface
     */
    private $oAuthCallbackHandler;

    /**
     * @param OAuthServiceInterface $oauthService
     * @param RequestTokenStorageInterface $requestTokenStorage
     * @param OAuthUrlHelper $oauthUrlHelper
     * @param OAuthCallbackHandlerInterface $oauthCallbackHandler
     */
    public function __construct(
        OAuthServiceInterface $oauthService,
        RequestTokenStorageInterface $requestTokenStorage,
        OAuthUrlHelper $oauthUrlHelper,
        OAuthCallbackHandlerInterface $oauthCallbackHandler
    ) {
        $this->oAuthService = $oauthService;
        $this->requestTokenStorage = $requestTokenStorage;
        $this->oAuthUrlHelper = $oauthUrlHelper;
        $this->oAuthCallbackHandler = $oauthCallbackHandler;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function connect(Request $request)
    {
        try {
            $callbackUrl = (string) $this->oAuthUrlHelper->createCallbackUri($request);
        } catch (\InvalidArgumentException $e) {
            return new Response($e->getMessage(), 400);
        }

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
        $this->requestTokenStorage->removeStoredRequestToken();

        if ($this->oAuthUrlHelper->hasValidRequestToken($request, $requestToken)) {
            return new Response('Invalid request token.', 500);
        }

        $accessToken = $this->oAuthService->getAccessToken(
            $requestToken,
            $this->oAuthUrlHelper->getOAuthVerifier($request)
        );

        try {
            $destination = $this->oAuthUrlHelper->getDestinationUri($request);
        } catch (\InvalidArgumentException $e) {
            return new Response($e->getMessage(), 400);
        }

        return $this->oAuthCallbackHandler->handle(
            $accessToken,
            $destination
        );
    }
}
