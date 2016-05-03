<?php
namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\Auth\User;
use GuzzleHttp\Psr7\Uri;
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
     * @var OAuthResponseFactoryInterface
     */
    private $oAuthResponseFactory;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        OAuthResponseFactoryInterface $oAuthResponseFactory = null
    ) {
        $this->urlGenerator = $urlGenerator;

        $this->oAuthResponseFactory = $oAuthResponseFactory;
    }
    
    /**
     * @param Request $request
     * @return StringLiteral|null
     */
    public function createCallbackUrl(Request $request)
    {
        $destination = $this->getDestination($request);
        $params = [];
        if ($destination) {
            $params[self::DESTINATION] = $destination->toNative();
        }

        $url = $this->urlGenerator->generate(
            self::AUTHORISATION_ROUTE_NAME,
            $params,
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new StringLiteral($url);
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
     * @return Uri
     */
    public function createDefaultUri(StringLiteral $defaultDestination)
    {
        return new Uri(
            $this->urlGenerator->generate(
                $defaultDestination->toNative()
            )
        );
    }

    /**
     * @param StringLiteral $destination
     * @return Uri
     */
    public function createDestinationUri(StringLiteral $destination)
    {
        return new Uri($destination->toNative());
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
