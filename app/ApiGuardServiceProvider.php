<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\CompositeApiKeyReader;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\CustomHeaderApiKeyReader;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\QueryParameterApiKeyReader;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\CultureFeedDecorator;
use ICultureFeed;

class ApiGuardServiceProvider extends BaseServiceProvider
{
    protected $provides = [
        ApiKeyReaderInterface::class,
        ICultureFeed::class,
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {

        $this->addShared(
            ApiKeyReaderInterface::class,
            function () {
                $queryReader = new QueryParameterApiKeyReader('apiKey');
                $headerReader = new CustomHeaderApiKeyReader('X-Api-Key');

                return new CompositeApiKeyReader(
                    $queryReader,
                    $headerReader
                );
            }
        );

        $this->addShared(
            ICultureFeed::class,
            function () {
                $oauthClient = new \CultureFeed_DefaultOAuthClient(
                    $this->parameter('uitid.consumer.key'),
                    $this->parameter('uitid.consumer.secret')
                );

                $oauthClient->setEndpoint($this->parameter('uitid.base_url'));
                return new CultureFeedDecorator($oauthClient);
            }
        );
    }
}
