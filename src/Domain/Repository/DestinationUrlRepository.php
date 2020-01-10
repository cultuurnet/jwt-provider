<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Repository;

use CultuurNet\UDB3\JwtProvider\Domain\Url;

interface DestinationUrlRepository
{
    public function storeDestinationUrl(Url $destinationUrl);

    public function getDestinationUrl(): ?Url;

    public function removeDestinationUrl(): void;
}
