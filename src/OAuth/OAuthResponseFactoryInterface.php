<?php

namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\Auth\User as AccessToken;
use GuzzleHttp\Psr7\Uri;
use Symfony\Component\HttpFoundation\Response;

interface OAuthResponseFactoryInterface
{
    /**
     * @param AccessToken $accessToken
     * @param Uri $destination
     * @return Response
     */
    public function create(AccessToken $accessToken, Uri $destination);
}