<?php

namespace CultuurNet\UDB3\JwtProvider\CultureFeed;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\User as AccessToken;
use ValueObjects\String\String as StringLiteral;

class CultureFeedFactory implements CultureFeedFactoryInterface
{
    /**
     * @var ConsumerCredentials
     */
    private $consumerCredentials;

    /**
     * @var StringLiteral
     */
    private $baseUrl;

    /**
     * @param ConsumerCredentials $consumerCredentials
     * @param StringLiteral $baseUrl
     */
    public function __construct(
        ConsumerCredentials $consumerCredentials,
        StringLiteral $baseUrl
    ) {
        $this->consumerCredentials = $consumerCredentials;

        $this->baseUrl = $baseUrl;
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
        $client = $this->createOAuthClient($userAccessToken);
        $client->setEndpoint($this->baseUrl->toNative());

        return new \CultureFeed(
            $client
        );
    }
}
