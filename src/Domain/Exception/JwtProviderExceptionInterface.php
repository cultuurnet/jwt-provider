<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Exception;

interface JwtProviderExceptionInterface extends \Throwable
{
    public function getHttpCode(): int;
}
