<?php

namespace CultuurNet\UDB3\JwtProvider\User;

class UserClaims
{
    private string $uid;

    private string $nick;

    private ?EmailAddress $email;

    public function __construct(
        string $uid,
        string $nick,
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
            'uid' => $this->uid,
            'nick' => $this->nick,
            'email' => $this->email !== null ? $this->email->toString() : '',
        ];
    }
}
