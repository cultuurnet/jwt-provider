<?php

namespace CultuurNet\UDB3\JwtProvider\CultureFeed;

use CultureFeed;
use CultureFeed_DefaultOAuthClient;
use CultuurNet\Auth\User as AccessToken;
use CultuurNet\UDB3\JwtProvider\Auth\ConsumerCredentials;
use ICultureFeed;

class CultureFeedFactory implements CultureFeedFactoryInterface
{
    private ConsumerCredentials $consumerCredentials;

    private string $baseUrl;

    public function __construct(
        ConsumerCredentials $consumerCredentials,
        string $baseUrl
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
        $client->setEndpoint($this->baseUrl);

        return new CultureFeed(
            $client
        );
    }
}
