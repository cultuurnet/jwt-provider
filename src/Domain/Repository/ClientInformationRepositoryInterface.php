<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Repository;

use CultuurNet\UDB3\JwtProvider\Domain\Value\ClientInformation;

interface ClientInformationRepositoryInterface
{
    public function store(ClientInformation $clientInformation): void;

    public function get(): ?ClientInformation;

    public function clear(): void;
}
