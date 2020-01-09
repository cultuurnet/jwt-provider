<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

interface AuthService
{
    public function redirectToLogin(): void;

    public function token(): ?string;
}
