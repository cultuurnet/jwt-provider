<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;
use Psr\Http\Message\ResponseInterface;

interface LoginServiceInterface
{
    public function redirectToLogin(): ?ResponseInterface;

    /**
     * @return string|null
     * @throws UnSuccessfulAuthException
     */
    public function token(): ?string;

    public function refreshToken(): ?string;
}
