<?php

namespace CultuurNet\UDB3\JwtProvider\User;

use CultuurNet\Auth\User as AccessToken;
use CultuurNet\UDB3\JwtProvider\Jwt\CultureFeed\CultureFeedFactoryInterface;
use ValueObjects\String\String as StringLiteral;
use ValueObjects\Web\EmailAddress;

class CultureFeedUserService implements UserServiceInterface
{
    /**
     * @var CultureFeedFactoryInterface
     */
    private $cultureFeedFactory;

    public function __construct(CultureFeedFactoryInterface $cultureFeedFactory)
    {
        $this->cultureFeedFactory = $cultureFeedFactory;
    }

    /**
     * @inheritdoc
     */
    public function getUserClaims(AccessToken $userAccessToken)
    {
        /* @var \CultureFeed_User $cfUser */
        $cfUser = $this->cultureFeedFactory
            ->createForUser($userAccessToken)
            ->getUser($userAccessToken->getId());

        return new UserClaims(
            new StringLiteral((string) $cfUser->id),
            new StringLiteral((string) $cfUser->nick),
            !is_null($cfUser->mbox) ? new EmailAddress($cfUser->mbox) : null
        );
    }
}
