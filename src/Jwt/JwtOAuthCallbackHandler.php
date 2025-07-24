<?php

namespace CultuurNet\UDB3\JwtProvider\Jwt;

use CultuurNet\UDB3\JwtProvider\Auth\User as AccessToken;
use CultuurNet\UDB3\JwtProvider\Http\RedirectResponse;
use CultuurNet\UDB3\JwtProvider\OAuth\OAuthCallbackHandlerInterface;
use CultuurNet\UDB3\JwtProvider\User\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class JwtOAuthCallbackHandler implements OAuthCallbackHandlerInterface
{
    private JwtEncoderService $encoderService;

    private UserServiceInterface $userService;

    public function __construct(
        JwtEncoderService $encoderService,
        UserServiceInterface $userService
    ) {
        $this->encoderService = $encoderService;
        $this->userService = $userService;
    }

    public function handle(AccessToken $accessToken, UriInterface $destination): ResponseInterface
    {
        $claims = $this->userService
            ->getUserClaims($accessToken)
            ->toArray();

        $jwt = $this->encoderService->encode($claims);

        $q = $destination->getQuery();

        $q .= ($q?'&':'') . 'jwt=' . $jwt;

        return new RedirectResponse(
            (string) $destination->withQuery($q)
        );
    }
}
