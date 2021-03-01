<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use Psr\Http\Message\ResponseInterface;

interface LogOutServiceInterface
{
    public function logout(): ?ResponseInterface;
}
