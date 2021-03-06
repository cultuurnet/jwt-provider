<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Value;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use Psr\Http\Message\UriInterface;

final class ClientInformation
{
    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * @var ApiKey|null
     */
    private $apiKey;

    /**
     * @var bool
     */
    private $isAllowedRefresh;

    public function __construct(UriInterface $uri, ApiKey $apiKey = null, bool $isAllowedRefresh = false)
    {
        $this->uri = $uri;
        $this->apiKey = $apiKey;
        $this->isAllowedRefresh = $isAllowedRefresh;
    }

    public function uri(): UriInterface
    {
        return $this->uri;
    }

    public function apiKey(): ?ApiKey
    {
        return $this->apiKey;
    }

    public function isAllowedRefresh(): bool
    {
        return $this->isAllowedRefresh;
    }
}
