<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Exception;

class InvalidDestinationException extends \Exception
{
    public const MESSAGE = 'Given destination url is invalid: ';

    public function __construct(string $url, $code = 0, Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE . $url, $code, $previous);
    }
}
