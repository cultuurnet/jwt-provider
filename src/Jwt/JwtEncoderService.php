<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Jwt;

use Lcobucci\JWT\Token;

interface JwtEncoderService
{
    public function encode(array $claims): Token;
}