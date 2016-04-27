<?php

namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\Auth\ServiceInterface as OAuthServiceInterface;
use CultuurNet\Auth\TokenCredentials as RequestToken;
use CultuurNet\Auth\User as AccessToken;
use CultuurNet\UDB3\JwtProvider\RequestTokenStorage\RequestTokenStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use ValueObjects\String\String as StringLiteral;

class OAuthController
{
    const DESTINATION = 'destination';
    const AUTHORISATION_ROUTE_NAME = 'uitid.oauth.authorize';

    const OAUTH_TOKEN = 'oauth_token';
    const OAUTH_VERIFIER = 'oauth_verifier';

    /**
     * @var OAuthServiceInterface
     */
    private $oAuthService;

    /**
     * @var RequestTokenStorageInterface
     */
    private $requestTokenStorage;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var StringLiteral
     */
    private $defaultDestination;

    public function __construct(
        OAuthServiceInterface $oAuthService,
        RequestTokenStorageInterface $requestTokenStorage,
        UrlGeneratorInterface $urlGenerator,
        StringLiteral $defaultDestination = null
    ) {
        $this->oAuthService = $oAuthService;

        $this->requestTokenStorage = $requestTokenStorage;

        $this->urlGenerator = $urlGenerator;

        $this->defaultDestination = $defaultDestination;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function connect(Request $request)
    {
        $callbackUrl = $this->createCallbackUrl($request);

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
            // TODO: Set the user information aka access token.
        }

        $destination = $this->getDestination($request);
        if ($destination) {
            $redirectResponse = new RedirectResponse($destination->toNative());
        } else {
            $redirectResponse = new RedirectResponse(
                $this->urlGenerator->generate(
                    $this->defaultDestination->toNative()
                )
            );
        }

        return $redirectResponse;
    }

    /**
     * @param Request $request
     * @return StringLiteral|null
     */
    private function getDestination(Request $request)
    {
        $destination = null;

        if ($request->query->get(self::DESTINATION)) {
            $destination = new StringLiteral(
                $request->query->get(self::DESTINATION)
            );
        }

        return $destination;
    }

    /**
     * @param Request $request
     * @return StringLiteral
     * @internal param StringLiteral $destination
     */
    private function createCallbackUrl(Request $request)
    {
        $destination = $this->getDestination($request);

        $url = $this->urlGenerator->generate(
            self::AUTHORISATION_ROUTE_NAME,
            [$destination->toNative()],
            UrlGeneratorInterface::ABSOLUTE_PATH
        );

        return new StringLiteral($url);
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

        if ($this->hasAccessToken($request, $requestToken)) {
            $accessToken = $this->oAuthService->getAccessToken(
                $requestToken,
                $request->query->get(self::OAUTH_VERIFIER)
            );
        }

        return $accessToken;
    }

    /**
     * @param Request $request
     * @param RequestToken $requestToken
     * @return bool
     */
    private function hasAccessToken(
        Request $request,
        RequestToken $requestToken
    ) {
        $token = $requestToken->getToken();

        $hasAccessToken = $request->query->get(self::OAUTH_TOKEN) == $token &&
            $request->query->get(self::OAUTH_VERIFIER);

        return $hasAccessToken;
    }
}
