<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use Psr\Http\Message\ResponseInterface;

interface AuthServiceInterface
{
    public function redirectToLogin(): ?ResponseInterface;

    public function token(): ?string;
}
