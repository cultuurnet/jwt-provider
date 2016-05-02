<?php
namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\Auth\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use ValueObjects\String\String as StringLiteral;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use CultuurNet\Auth\TokenCredentials as RequestToken;

class OAuthUrlHelper
{
    const DESTINATION = 'destination';
    const AUTHORISATION_ROUTE_NAME = 'uitid.oauth.authorize';
    
    const OAUTH_TOKEN = 'oauth_token';
    const OAUTH_VERIFIER = 'oauth_verifier';

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var DestinationModifierInterface
     */
    private $destinationModifier;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        DestinationModifierInterface $destinationModifier = null
    ) {
        $this->urlGenerator = $urlGenerator;

        $this->destinationModifier = $destinationModifier;
    }

    /**
     * @param Request $request
     * @param StringLiteral $defaultDestination
     * @param User $user
     * @return RedirectResponse
     */
    public function createAuthorizationResponse(
        Request $request,
        StringLiteral $defaultDestination,
        User $user
    ) {
        $destination = $this->getDestination($request);

        $realDestination = $destination ? $destination : $defaultDestination;
        if ($this->destinationModifier) {
            $realDestination = $this->destinationModifier->modify(
                $realDestination,
                $user
            );
        }

        if ($destination) {
            $redirectResponse = $this->createRedirect($realDestination);
        } else {
            $redirectResponse = $this->createDefaultRedirect($realDestination);
        }

        return $redirectResponse;
    }
    
    /**
     * @param Request $request
     * @return StringLiteral|null
     */
    public function createCallbackUrl(Request $request)
    {
        $callbackUrl = null;

        $destination = $this->getDestination($request);

        if ($destination) {
            $url = $this->urlGenerator->generate(
                self::AUTHORISATION_ROUTE_NAME,
                [self::DESTINATION => $destination->toNative()],
                UrlGeneratorInterface::ABSOLUTE_PATH
            );

            $callbackUrl = new StringLiteral($url);
        }

        return $callbackUrl;
    }
    
    /**
     * @param Request $request
     * @param RequestToken $requestToken
     * @return bool
     */
    public function hasValidAccessToken(
        Request $request,
        RequestToken $requestToken
    ) {
        $token = $requestToken->getToken();

        $actualToken = $request->query->get(self::OAUTH_TOKEN);
        $actualVerifier = $request->query->get(self::OAUTH_VERIFIER);

        $hasAccessToken = ($actualToken === $token) && (bool) $actualVerifier;

        return $hasAccessToken;
    }

    /**
     * @param StringLiteral $defaultDestination
     * @return RedirectResponse
     */
    private function createDefaultRedirect(StringLiteral $defaultDestination)
    {
        /* not sure why we need urlGenerator here, but not in createRedirect
           this is taken from the old implementation */
        return new RedirectResponse(
            $this->urlGenerator->generate(
                $defaultDestination->toNative()
            )
        );
    }

    /**
     * @param StringLiteral $destination
     * @return RedirectResponse
     */
    private function createRedirect(StringLiteral $destination)
    {
        return new RedirectResponse($destination->toNative());
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
}
