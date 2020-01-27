<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Repository;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;

interface ApiKeyRepositoryInterface
{
    public function storeApiKey(ApiKey $apiKey): void;

    public function getApiKey(): ?ApiKey;
}
