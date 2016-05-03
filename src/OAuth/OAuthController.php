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
        $this->oauthService = $oauthService;
        $this->requestTokenStorage = $requestTokenStorage;
        $this->oauthUrlHelper = $oauthUrlHelper;
        $this->oauthCallbackHandler = $oauthCallbackHandler;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function connect(Request $request)
    {
        try {
            $callbackUrl = (string) $this->oauthUrlHelper->createCallbackUri($request);
        } catch (\InvalidArgumentException $e) {
            return new Response($e->getMessage(), 400);
        }

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
        $this->requestTokenStorage->removeStoredRequestToken();

        if ($this->oauthUrlHelper->hasValidRequestToken($request, $requestToken)) {
            return new Response('Invalid request token.', 500);
        }

        $accessToken = $this->oauthService->getAccessToken(
            $requestToken,
            $this->oauthUrlHelper->getOAuthVerifier($request)
        );

        try {
            $destination = $this->oauthUrlHelper->getDestinationUri($request);
        } catch (\InvalidArgumentException $e) {
            return new Response($e->getMessage(), 400);
        }

        return $this->oauthCallbackHandler->handle(
            $accessToken,
            $destination
        );
    }
}
