<?php

namespace CultuurNet\UDB3\JwtProvider\CultureFeed;

use ValueObjects\String\String as StringLiteral;

interface UserServiceInterface
{
    /**
     * TODO: Return UserClaim object
     * @param StringLiteral $id
     * @return UserClaim
     */
    public function getUser(StringLiteral $id);
}