<?php

namespace CultuurNet\UDB3\JwtProvider\User;

use ValueObjects\String\String as StringLiteral;
use ValueObjects\Web\EmailAddress;

class CultureFeedUserService implements UserServiceInterface
{
    /**
     * @var \ICultureFeed
     */
    private $cultureFeed;

    public function __construct(\ICultureFeed $cultureFeed)
    {
        $this->cultureFeed = $cultureFeed;
    }

    /**
     * @inheritdoc
     */
    public function getUserClaims(StringLiteral $id)
    {
        /* @var \CultureFeed_User $cfUser */
        $cfUser = $this->cultureFeed->getUser($id->toNative());

        return new UserClaims(
            new StringLiteral((string) $cfUser->id),
            new StringLiteral((string) $cfUser->nick),
            !is_null($cfUser->mbox) ? new EmailAddress($cfUser->mbox) : null
        );
    }
}
