<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Repository;


use CultuurNet\UDB3\JwtProvider\Domain\DestinationUrl;

interface DestinationUrlRepository
{
    public function storeDestinationUrl(DestinationUrl $destinationUrl);

    public function getDestinationUrl(): ?DestinationUrl;

    public function removeDestinationUrl(): void;
}
