<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use CultureFeed;
use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKeyAuthenticatorInterface;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\CompositeApiKeyReader;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\CustomHeaderApiKeyReader;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\QueryParameterApiKeyReader;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerReadRepositoryInterface;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerWriteRepositoryInterface;
use CultuurNet\UDB3\ApiGuard\Consumer\InMemoryConsumerRepository;
use CultuurNet\UDB3\ApiGuard\CultureFeed\CultureFeedApiKeyAuthenticator;
use CultuurNet\UDB3\ApiGuard\Request\ApiKeyRequestAuthenticator;
use CultuurNet\UDB3\ApiGuard\Request\RequestAuthenticatorInterface;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Repository\ConsumerSession;
use ICultureFeed;

class ApiGuardServiceProvider extends BaseServiceProvider
{
    protected $provides = [
        ApiKeyReaderInterface::class,
        ApiKeyAuthenticatorInterface::class,
        RequestAuthenticatorInterface::class,
        InMemoryConsumerRepository::class,
        ConsumerWriteRepositoryInterface::class,
        ConsumerReadRepositoryInterface::class,
        ICultureFeed::class,
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {

        $this->add(
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
                $consumerCredentials = new ConsumerCredentials(
                    $this->parameter('uitid.consumer.key'),
                    $this->parameter('uitid.consumer.secret')
                );

                $oauthClient = new \CultureFeed_DefaultOAuthClient(
                    $consumerCredentials->getKey(),
                    $consumerCredentials->getSecret()
                );
                $oauthClient->setEndpoint($this->parameter('uitid.base_url'));
                return new CultureFeed($oauthClient);
            }
        );
        $this->add(
            ApiKeyAuthenticatorInterface::class,
            function () {
                $consumerCredentials = new ConsumerCredentials(
                    $this->parameter('uitid.consumer.key'),
                    $this->parameter('uitid.consumer.secret')
                );

                $oauthClient = new \CultureFeed_DefaultOAuthClient(
                    $consumerCredentials->getKey(),
                    $consumerCredentials->getSecret()
                );
                $oauthClient->setEndpoint($this->parameter('uitid.base_url'));

                return new CultureFeedApiKeyAuthenticator(
                    new CultureFeed($oauthClient),
                    $this->get(ConsumerSession::class),
                    true
                );
            }
        );

        $this->add(
            RequestAuthenticatorInterface::class,
            function () {
                return new ApiKeyRequestAuthenticator(
                    $this->get(ApiKeyReaderInterface::class),
                    $this->get(ApiKeyAuthenticatorInterface::class)
                );
            }
        );

        $this->addShared(
            ConsumerReadRepositoryInterface::class,
            function () {
                return $this->get(ConsumerSession::class);
            }
        );

        $this->addShared(
            ConsumerWriteRepositoryInterface::class,
            function () {
                return $this->get(ConsumerSession::class);
            }
        );

        $this->addShared(
            InMemoryConsumerRepository::class,
            function () {
                return new InMemoryConsumerRepository();
            }
        );
    }
}
