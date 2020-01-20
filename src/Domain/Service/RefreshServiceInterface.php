<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;

interface RefreshServiceInterface
{
    /**
     * @throws UnSuccessfulAuthException
     */
    public function renewToken();

    public function token(): string;
}
