<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerInterface;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerReadRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\IsAllowedRefreshTokenInterface;

final class IsAllowedRefreshToken implements IsAllowedRefreshTokenInterface
{
    private string $refreshGroupId;

    private \CultuurNet\UDB3\ApiGuard\Consumer\ConsumerReadRepositoryInterface $consumerReadRepository;

    public function __construct(
        ConsumerReadRepositoryInterface $consumerReadRepository,
        string $refreshGroupId
    ) {
        $this->refreshGroupId = $refreshGroupId;
        $this->consumerReadRepository = $consumerReadRepository;
    }

    public function __invoke(ApiKey $apiKey): bool
    {
        $consumer = $this->consumerReadRepository->getConsumer($apiKey);
        if ($consumer === null) {
            return false;
        }

        return $this->hasPermissionForRefresh($consumer);
    }

    private function hasPermissionForRefresh(ConsumerInterface $consumer): bool
    {
        foreach ($consumer->getPermissionGroupIds() as $group) {
            if ($group === $this->refreshGroupId) {
                return true;
            }
        }

        return false;
    }
}
