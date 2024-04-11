<?php

namespace CultuurNet\UDB3\JwtProvider\CultureFeed;

use CultureFeed;
use CultureFeed_DefaultOAuthClient;
use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\User as AccessToken;
use ICultureFeed;
use ValueObjects\StringLiteral\StringLiteral;

class CultureFeedFactory implements CultureFeedFactoryInterface
{
    private ConsumerCredentials $consumerCredentials;

    private StringLiteral $baseUrl;

    public function __construct(
        ConsumerCredentials $consumerCredentials,
        StringLiteral $baseUrl
    ) {
        $this->consumerCredentials = $consumerCredentials;

        $this->baseUrl = $baseUrl;
    }

    private function createOAuthClient(AccessToken $userAccessToken): CultureFeed_DefaultOAuthClient
    {
        return new CultureFeed_DefaultOAuthClient(
            $this->consumerCredentials->getKey(),
            $this->consumerCredentials->getSecret(),
            $userAccessToken->getTokenCredentials()->getToken(),
            $userAccessToken->getTokenCredentials()->getSecret()
        );
    }

    public function createForUser(AccessToken $userAccessToken): ICultureFeed
    {
        $client = $this->createOAuthClient($userAccessToken);
        $client->setEndpoint($this->baseUrl->toNative());

        return new CultureFeed(
            $client
        );
    }
}
