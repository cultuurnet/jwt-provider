<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use ValueObjects\StringLiteral\StringLiteral;

interface IsAllowedRefreshTokenInterface
{
    public function __invoke(StringLiteral $apiKey): bool;
}
