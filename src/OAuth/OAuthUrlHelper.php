<?php
namespace CultuurNet\UDB3\JwtProvider\OAuth;

use GuzzleHttp\Psr7\Uri;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use CultuurNet\Auth\TokenCredentials as RequestToken;

class OAuthUrlHelper
{
    private const DESTINATION = 'destination';
    private const OAUTH_TOKEN = 'oauth_token';
    private const OAUTH_VERIFIER = 'oauth_verifier';

    /**
     * @var string
     */
    private $authorizationPath;

    /**
     * @param string $authorizationPath
     */
    public function __construct(
        string $authorizationPath
    ) {
        $this->authorizationPath = trim($authorizationPath, '/');
    }

    public function createCallbackUri(ServerRequestInterface $request): UriInterface
    {
        $baseUrl = $this->getBaseUrlFromRequest($request);
        $query = http_build_query([self::DESTINATION => (string) $this->getDestinationUri($request)]);

        $url = $baseUrl . '/' . $this->authorizationPath . '?' . $query;

        return new Uri($url);
    }

    public function hasValidRequestToken(
        ServerRequestInterface $request,
        RequestToken $requestToken
    ): bool {
        $token = $requestToken->getToken();

        $actualToken = $request->getQueryParams()[self::OAUTH_TOKEN] ?? null;
        $actualVerifier = $this->getOAuthVerifier($request);

        return ($actualToken === $token) && (bool) $actualVerifier;
    }

    public function getOAuthVerifier(ServerRequestInterface $request): ?string
    {
        $verifier = $request->getQueryParams()[self::OAUTH_VERIFIER] ?? null;

        if ($verifier === null) {
            return $verifier;
        }

        return (string) $verifier;
    }

    public function getDestinationUri(ServerRequestInterface $request): UriInterface
    {
        $destination = $request->getQueryParams()[self::DESTINATION] ?? null;

        if (empty($destination)) {
            throw new InvalidArgumentException(
                'Request does not contain a destination parameter to redirect to after login.'
            );
        }

        return new Uri($destination);
    }

    private function getBaseUrlFromRequest(ServerRequestInterface $request): string
    {
        // Inspired by
        // https://github.com/slimphp/Slim/blob/200c6143f15baa477601879b64ab2326847aac0b/Slim/Http/Uri.php#L825
        $uri = $request->getUri();
        $scheme = $uri->getScheme();
        $host = $uri->getHost();
        return ($scheme !== '' ? $scheme . ':' : '') . ($host ? '//' . $host : '');
    }
}
