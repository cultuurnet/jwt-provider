<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;

interface IsAllowedRefreshTokenInterface
{
    public function __invoke(ApiKey $apiKey): bool;
}
