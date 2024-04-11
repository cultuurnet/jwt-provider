<?php

namespace CultuurNet\UDB3\JwtProvider\User;

use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class UserClaims
{
    private StringLiteral $uid;

    private StringLiteral $nick;

    private ?EmailAddress $email;

    public function __construct(
        StringLiteral $uid,
        StringLiteral $nick,
        ?EmailAddress $email = null
    ) {
        $this->uid = $uid;
        $this->nick = $nick;
        $this->email = $email;
    }

    public function toArray(): array
    {
        // Always set the email claim, but could be empty in some cases.
        return [
            'uid' => (string) $this->uid,
            'nick' => (string) $this->nick,
            'email' => (string) $this->email,
        ];
    }
}
