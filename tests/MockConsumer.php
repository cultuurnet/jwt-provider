<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerInterface;

final class MockConsumer implements ConsumerInterface
{
    private ApiKey $apiKey;

    /**
     * @var string[]
     */
    private array $permissionGroupIds = [];

    private ?string $defaultQuery = null;

    private ?string $name = null;

    public function __construct(ApiKey $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param string[] $permissionGroupIds
     */
    public function withPermissionGroupIds(array $permissionGroupIds): self
    {
        $c = clone $this;
        $c->permissionGroupIds = $permissionGroupIds;
        return $c;
    }

    public function withDefaultQuery(?string $defaultQuery): self
    {
        $c = clone $this;
        $c->defaultQuery = $defaultQuery;
        return $c;
    }

    public function withName(?string $name): self
    {
        $c = clone $this;
        $c->name = $name;
        return $c;
    }

    public function getApiKey(): ApiKey
    {
        return $this->apiKey;
    }

    public function getDefaultQuery(): ?string
    {
        return $this->defaultQuery;
    }

    /**
     * @return string[]
     */
    public function getPermissionGroupIds(): array
    {
        return $this->permissionGroupIds;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
