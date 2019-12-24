<?php

namespace CultuurNet\UDB3\JwtProvider\User;

use CultuurNet\Auth\User as AccessToken;

interface UserServiceInterface
{
    public function getUserClaims(AccessToken $userAccessToken): UserClaims;
}
