<?php
namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\Auth\AuthorizeOptions;
use CultuurNet\Auth\ServiceInterface as OAuthServiceInterface;
use CultuurNet\UDB3\JwtProvider\RequestTokenStorage\RequestTokenStorageInterface;
use Guzzle\Http\QueryString;
use Guzzle\Http\Url;
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
     * @var Url
     */
    private $oAuthBaseUrl;

    /**
     * @param OAuthServiceInterface $oAuthService
     * @param RequestTokenStorageInterface $requestTokenStorage
     * @param OAuthUrlHelper $oAuthUrlHelper
     * @param OAuthCallbackHandlerInterface $oAuthCallbackHandler
     * @param Url $oAuthBaseUrl
     */
    public function __construct(
        OAuthServiceInterface $oAuthService,
        RequestTokenStorageInterface $requestTokenStorage,
        OAuthUrlHelper $oAuthUrlHelper,
        OAuthCallbackHandlerInterface $oAuthCallbackHandler,
        Url $oAuthBaseUrl
    ) {
        $this->oAuthService = $oAuthService;
        $this->requestTokenStorage = $requestTokenStorage;
        $this->oAuthUrlHelper = $oAuthUrlHelper;
        $this->oAuthCallbackHandler = $oAuthCallbackHandler;
        $this->oAuthBaseUrl = $oAuthBaseUrl;
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

        $query = new QueryString(['destination' => $destination]);

        $logoutUrl = clone $this->oAuthBaseUrl
            ->addPath('auth/logout')
            ->setQuery($query);

        return RedirectResponse::create((string) $logoutUrl);
    }
}
