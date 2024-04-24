<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Jwt;

use Lcobucci\JWT\Token;

interface JwtDecoderService
{
    public function parse(string $tokenString): Token;

    public function validateData(Token $token): bool;

    public function validateRequiredClaims(Token $token): bool;

    public function verifySignature(Token $token): bool;
}