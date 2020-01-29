<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Exception;

use Fig\Http\Message\StatusCodeInterface;

class BadRequestException extends \Exception
{
    public static function missingRefreshToken(): BadRequestException
    {
        return new self('Missing refresh token', StatusCodeInterface::STATUS_BAD_REQUEST);
    }
}
