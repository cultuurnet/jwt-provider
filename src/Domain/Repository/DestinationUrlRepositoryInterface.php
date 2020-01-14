<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Repository;

use Psr\Http\Message\UriInterface;

interface DestinationUrlRepositoryInterface
{
    public function storeDestinationUrl(UriInterface $destinationUrl);

    public function getDestinationUrl(): ?UriInterface;

    public function removeDestinationUrl(): void;
}
