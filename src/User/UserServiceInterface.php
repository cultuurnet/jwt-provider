<?php

namespace CultuurNet\UDB3\JwtProvider\User;

use CultuurNet\Auth\User as AccessToken;

interface UserServiceInterface
{
    /**
     * @param AccessToken $userAccessToken
     * @return UserClaims
     */
    public function getUserClaims(AccessToken $userAccessToken);
}
