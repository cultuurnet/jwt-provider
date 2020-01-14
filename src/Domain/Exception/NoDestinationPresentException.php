<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Exception;

use Throwable;

class NoDestinationPresentException extends \Exception
{
    public const MESSAGE = 'Request does not contain a destination parameter to redirect to after login.';

    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(
            self::MESSAGE,
            $code,
            $previous
        );
    }
}
