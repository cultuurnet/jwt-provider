<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulRefreshException;

interface RefreshServiceInterface
{
    /**
     * @param string $refreshToken
     * @return string
     * @throws UnSuccessfulRefreshException
     */
    public function token(string $refreshToken): string;
}
