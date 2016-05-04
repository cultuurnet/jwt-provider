<?php

namespace CultuurNet\UDB3\JwtProvider\Jwt\CultureFeed;

use CultuurNet\Auth\User as AccessToken;
use ICultureFeed;

interface CultureFeedFactoryInterface
{
    /**
     * @param AccessToken $userAccessToken
     * @return ICultureFeed
     */
    public function createForUser(AccessToken $userAccessToken);
}
