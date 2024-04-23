<?php
namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\Auth\AuthorizeOptions;
use CultuurNet\UDB3\JwtProvider\Http\RedirectResponse;
use CultuurNet\UDB3\JwtProvider\Http\ResponseFactory;
use CultuurNet\UDB3\JwtProvider\RequestTokenStorage\RequestTokenStorageInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OAuthController
{
    private OAuthService $oAuthService;

    private RequestTokenStorageInterface $requestTokenStorage;

    private OAuthUrlHelper $oAuthUrlHelper;

    private OAuthCallbackHandlerInterface $oAuthCallbackHandler;

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

    public function connect(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $callbackUrl = (string) $this->oAuthUrlHelper->createCallbackUri($request);
        } catch (InvalidArgumentException $e) {
            return ResponseFactory::create(400, $e->getMessage());
        }

        $requestToken = $this->oAuthService->getRequestToken($callbackUrl);
        $this->requestTokenStorage->storeRequestToken($requestToken);

        $options = new AuthorizeOptions();
        $options->setSkipConfirmation();
        $options->setLang($request->getQueryParams()['lang'] ?? null);

        $authorizeUrl = $this->oAuthService->getAuthorizeUrl($requestToken, $options);
        return new RedirectResponse($authorizeUrl);
    }

    public function register(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $callbackUrl = (string) $this->oAuthUrlHelper->createCallbackUri($request);
        } catch (InvalidArgumentException $e) {
            return ResponseFactory::create(400, $e->getMessage());
        }

        $requestToken = $this->oAuthService->getRequestToken($callbackUrl);
        $this->requestTokenStorage->storeRequestToken($requestToken);

        $options = new AuthorizeOptions();
        $options->setTypeRegister();
        $options->setSkipAuthorization();
        $options->setLang($request->getQueryParams()['lang'] ?? null);

        $authorizeUrl = $this->oAuthService->getAuthorizeUrl($requestToken, $options);
        return new RedirectResponse($authorizeUrl);
    }

    public function authorize(ServerRequestInterface $request): ResponseInterface
    {
        $requestToken = $this->requestTokenStorage->getStoredRequestToken();
        $this->requestTokenStorage->removeStoredRequestToken();

        if ($requestToken === null ||
            !$this->oAuthUrlHelper->hasValidRequestToken($request, $requestToken)) {
            return ResponseFactory::create(500, 'Invalid request token.');
        }

        $accessToken = $this->oAuthService->getAccessToken(
            $requestToken,
            $this->oAuthUrlHelper->getOAuthVerifier($request)
        );

        try {
            $destination = $this->oAuthUrlHelper->getDestinationUri($request);
        } catch (InvalidArgumentException $e) {
            return ResponseFactory::create(400, $e->getMessage());
        }

        return $this->oAuthCallbackHandler->handle(
            $accessToken,
            $destination
        );
    }

    public function logout(ServerRequestInterface $request): ResponseInterface
    {
        $destination = $this->oAuthUrlHelper->getDestinationUri($request);

        $logoutUrl = $this->oAuthService->getLogoutUrl($destination);

        return new RedirectResponse((string) $logoutUrl);
    }
}
