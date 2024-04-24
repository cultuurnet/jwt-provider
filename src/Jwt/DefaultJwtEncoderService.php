<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Jwt;

use CultuurNet\UDB3\JwtProvider\Clock\Clock;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;

final class DefaultJwtEncoderService implements JwtEncoderService
{
    private Builder $builder;

    private Signer $signer;

    private Key $key;

    private Clock $clock;

    private int $exp;

    private int $nbf;

    public function __construct(
        Builder $builder,
        Signer $signer,
        Key $key,
        Clock $clock,
        int $exp,
        int $nbf = null
    ) {
        $this->builder = $builder;
        $this->signer = $signer;
        $this->key = $key;
        $this->clock = $clock;
        $this->exp = $exp;
        $this->nbf = !is_null($nbf) ? $nbf : 0;
    }

    public function encode(array $claims): Token
    {
        $builder = clone $this->builder;

        foreach ($claims as $claim => $value) {
            $builder->set($claim, $value);
        }

        $dateTime = $this->clock->getDateTime();
        $time = $dateTime->getTimestamp();

        $jwt = $builder
            ->setIssuedAt($time)
            ->setExpiration($time + $this->exp)
            ->setNotBefore($time + $this->nbf)
            ->sign(
                $this->signer,
                $this->key
            )
            ->getToken();

        return $jwt;
    }
}