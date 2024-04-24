<?php

namespace CultuurNet\UDB3\JwtProvider\User;

use CultuurNet\Auth\User as AccessToken;
use CultuurNet\UDB3\JwtProvider\CultureFeed\CultureFeedFactoryInterface;
use ValueObjects\Web\EmailAddress;

class CultureFeedUserService implements UserServiceInterface
{
    private CultureFeedFactoryInterface $cultureFeedFactory;

    public function __construct(CultureFeedFactoryInterface $cultureFeedFactory)
    {
        $this->cultureFeedFactory = $cultureFeedFactory;
    }

    public function getUserClaims(AccessToken $userAccessToken): UserClaims
    {
        /* @var \CultureFeed_User $cfUser */
        $cfUser = $this->cultureFeedFactory
            ->createForUser($userAccessToken)
            ->getUser($userAccessToken->getId(), true, true);

        return new UserClaims(
            $cfUser->id,
            $cfUser->nick,
            $cfUser->mbox !== null ? new EmailAddress($cfUser->mbox) : null
        );
    }
}
