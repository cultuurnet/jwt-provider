<?php

namespace CultuurNet\UDB3\JwtProvider\CultureFeed;

use CultuurNet\Auth\User as AccessToken;
use ICultureFeed;

interface CultureFeedFactoryInterface
{
    public function createForUser(AccessToken $userAccessToken): ICultureFeed;
}
