<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use CultureFeed;
use CultuurNet\UDB3\ApiGuard\CultureFeed\CultureFeedConsumerAdapter;

/**
 * "Decorator" - ugly hack, to enable IsAllowedRefreshToken
 * to be testable.
 */
class CultureFeedDecorator extends CultureFeed
{
    public function getServiceConsumerByApiKey($apiKey, $includePermissions = true)
    {
        return new CultureFeedConsumerAdapter(parent::getServiceConsumerByApiKey($apiKey, $includePermissions));
    }
}
