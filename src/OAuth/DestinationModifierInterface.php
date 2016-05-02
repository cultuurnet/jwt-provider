<?php

namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\Auth\User;
use ValueObjects\String\String as StringLiteral;

interface DestinationModifierInterface
{
    /**
     * @param StringLiteral $destination
     * @param User $user
     * @return StringLiteral
     */
    public function modify(StringLiteral $destination, User $user);
}
