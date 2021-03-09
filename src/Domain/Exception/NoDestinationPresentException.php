<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Exception;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Throwable;

final class NoDestinationPresentException extends Exception implements JwtProviderExceptionInterface
{
    public const MESSAGE = 'Request does not contain a destination parameter to redirect to after login.';

    public function __construct(int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            self::MESSAGE,
            $code,
            $previous
        );
    }

    public function getHttpCode(): int
    {
        return StatusCodeInterface::STATUS_BAD_REQUEST;
    }
}
