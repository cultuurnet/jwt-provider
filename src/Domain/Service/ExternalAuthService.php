<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

interface ExternalAuthService
{
    public function redirectToLogin(): void;
}
