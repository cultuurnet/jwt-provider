<?php
namespace CultuurNet\UDB3\JwtProvider\OAuth;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Request $request
     * @return UriInterface
     */
    public function createCallbackUri(Request $request)
    {
        $url = $this->urlGenerator->generate(
            self::AUTHORISATION_ROUTE_NAME,
            [
                self::DESTINATION => (string) $this->getDestinationUri($request),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new Uri($url);
    }

    /**
     * @param Request $request
     * @param RequestToken $requestToken
     * @return bool
     */
    public function hasValidRequestToken(
        Request $request,
        RequestToken $requestToken
    ) {
        $token = $requestToken->getToken();

        $actualToken = $request->query->get(self::OAUTH_TOKEN);
        $actualVerifier = $this->getOAuthVerifier($request);

        $hasAccessToken = ($actualToken === $token) && (bool) $actualVerifier;

        return $hasAccessToken;
    }

    /**
     * @param Request $request
     * @return string|null
     */
    public function getOAuthVerifier(Request $request)
    {
        return $request->query->get(OAuthUrlHelper::OAUTH_VERIFIER);
    }

    /**
     * @param Request $request
     * @return UriInterface
     */
    public function getDestinationUri(Request $request)
    {
        $destination = $request->query->get(self::DESTINATION);

        if (empty($destination)) {
            throw new \InvalidArgumentException(
                'Request does not contain a destination parameter to redirect to after login.'
            );
        }

        return new Uri($destination);
    }
}
