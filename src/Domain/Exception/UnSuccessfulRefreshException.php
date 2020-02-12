<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Exception;

use Fig\Http\Message\StatusCodeInterface;

class UnSuccessfulRefreshException extends \Exception implements JwtProviderExceptionInterface
{
    public function getHttpCode(): int
    {
        return StatusCodeInterface::STATUS_UNAUTHORIZED;
    }
}
