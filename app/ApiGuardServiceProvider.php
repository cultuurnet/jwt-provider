<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use CultureFeed;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\CompositeApiKeyReader;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\CustomHeaderApiKeyReader;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\QueryParameterApiKeyReader;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerReadRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\CultureFeedConsumerReadRepository;
use ICultureFeed;

final class ApiGuardServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
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
            ConsumerReadRepositoryInterface::class,
            function () {
                return new CultureFeedConsumerReadRepository($this->get(ICultureFeed::class));
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
                return new CultureFeed($oauthClient);
            }
        );
    }
}
