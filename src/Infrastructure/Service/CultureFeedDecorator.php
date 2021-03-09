<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use CultureFeed;
use CultureFeed_Consumer;
use CultuurNet\UDB3\ApiGuard\CultureFeed\CultureFeedConsumerAdapter;

/**
 * "Decorator" - ugly hack, to enable IsAllowedRefreshToken
 * to be testable.
 */
final class CultureFeedDecorator extends CultureFeed
{
    /**
     * @param string $apiKey
     * @param bool $includePermissions
     * @return CultureFeed_Consumer
     * @throws \CultureFeed_ParseException
     */
    public function getServiceConsumerByApiKey($apiKey, $includePermissions = true): CultureFeed_Consumer
    {
        /** @var CultureFeed_Consumer $cultureFeed_Consumer */
        $cultureFeed_Consumer = new CultureFeedConsumerAdapter(parent::getServiceConsumerByApiKey($apiKey, $includePermissions));
        return $cultureFeed_Consumer;
    }
}
