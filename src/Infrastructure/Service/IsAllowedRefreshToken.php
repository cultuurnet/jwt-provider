<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\IsAllowedRefreshTokenInterface;
use ValueObjects\StringLiteral\StringLiteral;

final class IsAllowedRefreshToken implements IsAllowedRefreshTokenInterface
{
    /**
     * @var string
     */
    private $refreshGroupId;

    /**
     * @var \ICultureFeed
     */
    private $cultureFeed;


    public function __construct(
        \ICultureFeed $cultureFeed,
        string $refreshGroupId
    ) {
        $this->refreshGroupId = $refreshGroupId;
        $this->cultureFeed = $cultureFeed;
    }

    public function __invoke(StringLiteral $apiKey): bool
    {
        $consumer = $this->cultureFeed->getServiceConsumerByApiKey(
            $apiKey->toNative()
        );

        if ($consumer === null) {
            return false;
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
