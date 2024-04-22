<?php

namespace CultuurNet\UDB3\JwtProvider\Jwt;

use CultuurNet\Auth\User as AccessToken;
use CultuurNet\UDB3\Jwt\JwtEncoderServiceInterface;
use CultuurNet\UDB3\JwtProvider\Http\RedirectResponse;
use CultuurNet\UDB3\JwtProvider\OAuth\OAuthCallbackHandlerInterface;
use CultuurNet\UDB3\JwtProvider\User\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class JwtOAuthCallbackHandler implements OAuthCallbackHandlerInterface
{
    private JwtEncoderServiceInterface $encoderService;

    private UserServiceInterface $userService;

    public function __construct(
        JwtEncoderServiceInterface $encoderService,
        UserServiceInterface $userService
    ) {
        $this->encoderService = $encoderService;
        $this->userService = $userService;
    }

    public function handle(AccessToken $accessToken, string $destination): ResponseInterface
    {
        $claims = $this->userService
            ->getUserClaims($accessToken)
            ->toArray();

        $jwt = $this->encoderService->encode($claims);

        $destination = $this->addJwtTokenToRedirectUri($destination, $jwt);

        return new RedirectResponse(
            $destination
        );
    }

    private function addJwtTokenToRedirectUri(string $destination, \Lcobucci\JWT\Token $jwt)
    {
        $urlParts = parse_url($destination);

        if (empty($urlParts['query'])) {
            $newQuery = 'jwt=' . $jwt;
        } else {
            $newQuery = $urlParts['query'] . '&jwt=' . $jwt;
        }

        $destination = str_replace($urlParts['query'], $newQuery, $destination);
        return $destination;
    }
}
