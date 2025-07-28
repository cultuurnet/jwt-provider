<?php

namespace CultuurNet\UDB3\JwtProvider\User;

use CultuurNet\UDB3\JwtProvider\Auth\User as AccessToken;

interface UserServiceInterface
{
    public function getUserClaims(AccessToken $userAccessToken): UserClaims;
}
