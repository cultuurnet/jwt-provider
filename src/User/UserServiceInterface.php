<?php

namespace CultuurNet\UDB3\JwtProvider\User;

use ValueObjects\String\String as StringLiteral;

interface UserServiceInterface
{
    /**
     * @param StringLiteral $id
     * @return UserClaims
     */
    public function getUserClaims(StringLiteral $id);
}
