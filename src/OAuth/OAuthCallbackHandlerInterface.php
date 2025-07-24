<?php

namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\UDB3\JwtProvider\Auth\User as AccessToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

interface OAuthCallbackHandlerInterface
{
    public function handle(AccessToken $accessToken, UriInterface $destination): ResponseInterface;
}
