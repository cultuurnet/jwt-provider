<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Jwt;

class JwtParserException extends \InvalidArgumentException
{
    public function __construct($e)
    {
        parent::__construct($e->getMessage(), 403);
    }
}