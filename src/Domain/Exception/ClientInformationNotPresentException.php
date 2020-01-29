<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Exception;

use Fig\Http\Message\StatusCodeInterface;

class ClientInformationNotPresentException extends \Exception implements JwtProviderExceptionInterface
{

    public function getHttpCode(): int
    {
        return StatusCodeInterface::STATUS_BAD_REQUEST;
    }
}
