<?php
namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\Auth\AuthorizeOptions;
use CultuurNet\UDB3\JwtProvider\RequestTokenStorage\RequestTokenStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OAuthController
{
    /**
     * @var OAuthService
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
     * @param OAuthService $oAuthService
     * @param RequestTokenStorageInterface $requestTokenStorage
     * @param OAuthUrlHelper $oAuthUrlHelper
     * @param OAuthCallbackHandlerInterface $oAuthCallbackHandler
     */
    public function __construct(
        OAuthService $oAuthService,
        RequestTokenStorageInterface $requestTokenStorage,
        OAuthUrlHelper $oAuthUrlHelper,
        OAuthCallbackHandlerInterface $oAuthCallbackHandler
    ) {
        $this->oAuthService = $oAuthService;
        $this->requestTokenStorage = $requestTokenStorage;
        $this->oAuthUrlHelper = $oAuthUrlHelper;
        $this->oAuthCallbackHandler = $oAuthCallbackHandler;
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

        $options = new AuthorizeOptions();
        $options->setSkipConfirmation();
        $options->setLang($request->query->get('lang'));

        $authorizeUrl = $this->oAuthService->getAuthorizeUrl($requestToken, $options);
        return new RedirectResponse($authorizeUrl);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function register(Request $request)
    {
        try {
            $callbackUrl = (string) $this->oAuthUrlHelper->createCallbackUri($request);
        } catch (\InvalidArgumentException $e) {
            return new Response($e->getMessage(), 400);
        }

        $requestToken = $this->oAuthService->getRequestToken($callbackUrl);
        $this->requestTokenStorage->storeRequestToken($requestToken);

        $options = new AuthorizeOptions();
        $options->setTypeRegister();
        $options->setSkipAuthorization();
        $options->setLang($request->query->get('lang'));

        $authorizeUrl = $this->oAuthService->getAuthorizeUrl($requestToken, $options);
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

        if ($requestToken === null ||
            !$this->oAuthUrlHelper->hasValidRequestToken($request, $requestToken)) {
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

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request)
    {
        $destination = $this->oAuthUrlHelper->getDestinationUri($request);

        $logoutUrl = $this->oAuthService->getLogoutUrl($destination);

        return RedirectResponse::create((string) $logoutUrl);
    }
}
