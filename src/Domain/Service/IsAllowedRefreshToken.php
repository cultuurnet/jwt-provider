<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerInterface;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerReadRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\InvalidApiKeyException;

class IsAllowedRefreshToken
{
    /**
     * @var ConsumerReadRepositoryInterface
     */
    private $consumerReadRepository;

    /**
     * @var string
     */
    private $refreshGroupId;


    public function __construct(
        ConsumerReadRepositoryInterface $consumerReadRepository,
        string $refreshGroupId
    ) {
        $this->consumerReadRepository = $consumerReadRepository;
        $this->refreshGroupId = $refreshGroupId;
    }

    public function __invoke(ApiKey $apiKey): bool
    {
        $consumer = $this->consumerReadRepository->getConsumer($apiKey);

        if ($consumer === null) {
            throw new InvalidApiKeyException();
        }

        return $this->hasPermissionForRefresh($consumer);
    }

    private function hasPermissionForRefresh(ConsumerInterface $consumer): bool
    {
        foreach ($consumer->getPermissionGroupIds() as $group) {
            if ($group->toNative() === $this->refreshGroupId) {
                return true;
            }
        }

        return false;
    }
}
