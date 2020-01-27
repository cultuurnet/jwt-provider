<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Repository;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ApiKeyRepositoryInterface;

class ApiKeySession implements ApiKeyRepositoryInterface
{

    public function storeApiKey(ApiKey $apiKey): void
    {
        // TODO: Implement storeApiKey() method.
    }

    public function getApiKey(): ?ApiKey
    {
        // TODO: Implement getApiKey() method.
    }
}
