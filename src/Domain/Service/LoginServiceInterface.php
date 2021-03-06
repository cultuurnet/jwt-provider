<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultuurNet\UDB3\JwtProvider\Domain\Enum\Locale;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;
use Psr\Http\Message\ResponseInterface;

interface LoginServiceInterface
{
    public function redirectToLogin(string $locale = Locale::DUTCH): ?ResponseInterface;

    /**
     * @throws UnSuccessfulAuthException
     */
    public function token(): ?string;

    /**
     * @throws UnSuccessfulAuthException
     */
    public function refreshToken(): ?string;
}
