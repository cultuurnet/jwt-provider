<?php

namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\UDB3\JwtProvider\Auth\AuthorizeOptions;
use CultuurNet\UDB3\JwtProvider\Auth\AuthorizeOptionsQueryString;
use CultuurNet\UDB3\JwtProvider\Auth\ConsumerCredentials;
use CultuurNet\UDB3\JwtProvider\Auth\TokenCredentials;
use CultuurNet\UDB3\JwtProvider\Auth\User;
use Guzzle\Http\QueryString;
use Guzzle\Http\Url;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Psr\Http\Message\ResponseInterface;

class OAuthService
{
    private Url $baseUrl;
    private ConsumerCredentials $consumerCredentials;

    public function __construct($baseUrl, ConsumerCredentials $consumerCredentials)
    {
        $this->baseUrl = Url::factory($baseUrl);
        $this->consumerCredentials = $consumerCredentials;
    }

    public function getLogoutUrl(string $destination): Url
    {
        $url = $this->getUrlForPath('/auth/logout');
        $url->setQuery(
            new QueryString(['destination' => $destination])
        );
        return $url;
    }

    public function getRequestToken($callback = null) {
        $data = [];
        if ($callback) {
            $data['oauth_callback'] = $callback;
        }

        $response = $this->sendPostRequest('requestToken', $data, null);

        // @todo Check for status 400 or 401 and throw an appropriate exception.
        // @todo Any other non-200 code is unexpected according to http://oauth.net/core/1.0a/ and should cause another kind of exception to be thrown.

        if ($response->getHeaderLine('Content-Type') !== 'application/x-www-form-urlencoded') {
            // @todo throw exception
        }

        parse_str((string) $response->getBody(), $q);

        // @todo check if valid response
        $token = $q['oauth_token'];
        $secret = $q['oauth_token_secret'];

        if (!isset($q['oauth_callback_confirmed']) || $q['oauth_callback_confirmed'] !== 'true') {
            // @todo throw an exception
        }

        $tokenPair = new TokenCredentials($token, $secret);

        return $tokenPair;
    }

    /**
     * Get the URL of the authorization page to redirect the user to.
     *
     * @param TokenCredentials $temporaryCredentials
     *   Temporary credentials fetched with getRequestToken.
     * @param AuthorizeOptions $options
     *   Miscellaneous options accepted in the URL.
     * @return string
     *   The URL of the authorization page.
     */
    public function getAuthorizeUrl(TokenCredentials $temporaryCredentials, AuthorizeOptions $options = NULL) {
        // @todo check if token is not empty
        if ($options) {
            $query = AuthorizeOptionsQueryString::fromAuthorizeOptions($options);
        }
        else {
            $query = new AuthorizeOptionsQueryString();
        }

        $query->set('oauth_token', $temporaryCredentials->getToken());

        $url = $this->getUrlForPath('auth/authorize');
        $url->setQuery($query);

        return (string) $url;
    }

    /**
     * Fetches token credentials (access token and secret).
     *
     * @param TokenCredentials $temporaryCredentials
     *   The temporary token credentials (request token & secret).
     * @param string $oAuthVerifier
     *   The 'oauth_verifier' that was retrieved in the OAUth authorization step.
     * @return User
     */
    public function getAccessToken(TokenCredentials $temporaryCredentials, $oAuthVerifier) {
        $data = array(
            'oauth_verifier' => $oAuthVerifier,
        );

        $response = $this->sendPostRequest('accessToken', $data, $temporaryCredentials);

        $body = $response->getBody();

        parse_str($body, $q);
        // @todo check if valid response
        $token = $q['oauth_token'];
        $secret = $q['oauth_token_secret'];
        $userId = $q['userId'];

        $tokenCredentials = new TokenCredentials($token, $secret);

        $user = new User($userId, $tokenCredentials);

        return $user;
    }

    private function createClient(Url $baseUrl, ConsumerCredentials $consumerCredentials, ?TokenCredentials $tokenCredentials = null): Client
    {
        $oAuthMiddleware = new Oauth1([
            'consumer_key' => $consumerCredentials->getKey(),
            'consumer_secret' => $consumerCredentials->getSecret(),
            'token' => $tokenCredentials ? $tokenCredentials->getToken() : '',
            'token_secret' => $tokenCredentials ? $tokenCredentials->getSecret() : '',
        ]);

        $stack = HandlerStack::create();
        $stack->push($oAuthMiddleware);

        return new Client([
            'base_uri' => (string) $baseUrl,
            'handler' => $stack,
            'auth' => 'oauth',
        ]);
    }

    private function getUrlForPath($path) {
        // @todo check type of $path
        $url = clone $this->baseUrl;
        $url->addPath($path);

        return $url;
    }

    private function sendPostRequest(string $path, array $formData, ?TokenCredentials $tokenCredentials): ResponseInterface
    {
        $client = $this->createClient($this->baseUrl, $this->consumerCredentials, $tokenCredentials);

        // Make sure to encode the form data using Query::build() and use the "body" option, as opposed to using the
        // "form_params" option. While form_params supports parameters with multiple values, it encodes them with a []
        // suffix which is not supported by UiTiD v1.
        $options = [
            'http_errors' => false,
            'headers' => ['content-type' => 'application/x-www-form-urlencoded'],
            'body' => Query::build($formData),
        ];
        $response = $client->request('POST', $path, $options);

        $status = $response->getStatusCode();
        if ($status < 200 || $status > 299) {
            throw new \Exception($response->getReasonPhrase(), $status);
        }

        return $response;
    }
}
