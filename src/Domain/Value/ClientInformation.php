<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Value;

use Psr\Http\Message\UriInterface;
use ValueObjects\StringLiteral\StringLiteral;

class ClientInformation
{
    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * @var StringLiteral
     */
    private $apiKey;

    /**
     * @var bool
     */
    private $isAllowedRefresh;

    public function __construct(UriInterface $uri, StringLiteral $apiKey = null, bool $isAllowedRefresh = false)
    {
        $this->uri = $uri;
        $this->apiKey = $apiKey;
        $this->isAllowedRefresh = $isAllowedRefresh;
    }

    public function uri(): UriInterface
    {
        return $this->uri;
    }

    public function apiKey(): ?StringLiteral
    {
        return $this->apiKey;
    }

    /**
     * @return bool
     */
    public function isAllowedRefresh(): bool
    {
        return $this->isAllowedRefresh;
    }
}
