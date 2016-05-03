<?php

namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\Auth\User as AccessToken;
use Psr\Http\Message\UriInterface;
use Symfony\Component\HttpFoundation\Response;

interface OAuthCallbackHandlerInterface
{
    /**
     * @param AccessToken $accessToken
     * @param UriInterface $destination
     * @return Response
     */
    public function handle(AccessToken $accessToken, UriInterface $destination);
}
