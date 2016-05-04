<?php

namespace CultuurNet\UDB3\JwtProvider\Jwt;

use CultuurNet\Auth\User as AccessToken;
use CultuurNet\UDB3\Jwt\JwtEncoderServiceInterface;
use CultuurNet\UDB3\JwtProvider\OAuth\OAuthCallbackHandlerInterface;
use CultuurNet\UDB3\JwtProvider\User\UserServiceInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @param JwtEncoderServiceInterface $encoderService
     * @param UserServiceInterface $userService
     */
    public function __construct(
        JwtEncoderServiceInterface $encoderService,
        UserServiceInterface $userService
    ) {
        $this->encoderService = $encoderService;
        $this->userService = $userService;
    }

    /**
     * @param AccessToken $accessToken
     * @param UriInterface $destination
     * @return Response
     */
    public function handle(AccessToken $accessToken, UriInterface $destination)
    {
        $claims = $this->userService
            ->getUserClaims($accessToken)
            ->toArray();

        $jwt = $this->encoderService->encode($claims);

        return new RedirectResponse(
            (string) $destination->withFragment('jwt=' . $jwt)
        );
    }
}
