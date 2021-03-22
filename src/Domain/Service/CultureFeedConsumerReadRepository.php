<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultureFeed_HttpException;
use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerInterface;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerReadRepositoryInterface;
use CultuurNet\UDB3\ApiGuard\CultureFeed\CultureFeedConsumerAdapter;
use ICultureFeed;

final class CultureFeedConsumerReadRepository implements ConsumerReadRepositoryInterface
{
    /**
     * @var ICultureFeed
     */
    private $cultureFeed;

    public function __construct(ICultureFeed $cultureFeed)
    {
        $this->cultureFeed = $cultureFeed;
    }

    public function getConsumer(ApiKey $apiKey): ?ConsumerInterface
    {
        try {
            $consumer = $this->cultureFeed->getServiceConsumerByApiKey($apiKey->toString(), true);
        } catch (CultureFeed_HttpException $e) {
            if ($e->getCode() === 404) {
                return null;
            }
            throw $e;
        }

        return new CultureFeedConsumerAdapter($consumer);
    }
}
