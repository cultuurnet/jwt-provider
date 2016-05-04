<?php

namespace CultuurNet\UDB3\JwtProvider\Jwt\CultureFeed;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\User as AccessToken;

class CultureFeedFactory implements CultureFeedFactoryInterface
{
    /**
     * @var ConsumerCredentials
     */
    private $consumerCredentials;

    /**
     * @param ConsumerCredentials $consumerCredentials
     */
    public function __construct(
        ConsumerCredentials $consumerCredentials
    ) {
        $this->consumerCredentials = $consumerCredentials;
    }

    /**
     * @param AccessToken $userAccessToken
     * @return \CultureFeed_DefaultOAuthClient
     */
    private function createOAuthClient(AccessToken $userAccessToken)
    {
        return new \CultureFeed_DefaultOAuthClient(
            $this->consumerCredentials->getKey(),
            $this->consumerCredentials->getSecret(),
            $userAccessToken->getTokenCredentials()->getToken(),
            $userAccessToken->getTokenCredentials()->getSecret()
        );
    }

    /**
     * @param AccessToken $userAccessToken
     * @return \CultureFeed
     */
    public function createForUser(AccessToken $userAccessToken)
    {
        return new \CultureFeed(
            $this->createOAuthClient($userAccessToken)
        );
    }
}
