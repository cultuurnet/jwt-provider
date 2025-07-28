<?php

namespace CultuurNet\UDB3\JwtProvider\CultureFeed;

use CultuurNet\UDB3\JwtProvider\Auth\User as AccessToken;
use ICultureFeed;

interface CultureFeedFactoryInterface
{
    public function createForUser(AccessToken $userAccessToken): ICultureFeed;
}
