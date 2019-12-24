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
    /**
     * @var JwtEncoderServiceInterface
     */
    private $encoderService;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(
        JwtEncoderServiceInterface $encoderService,
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
