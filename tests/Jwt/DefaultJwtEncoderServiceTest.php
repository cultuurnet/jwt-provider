<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Jwt;

use CultuurNet\UDB3\JwtProvider\Clock\FrozenClock;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use PHPUnit\Framework\TestCase;

class DefaultJwtEncoderServiceTest extends TestCase
{
    private string $tokenString;
    private JwtEncoderService $encoderService;

    public function setUp(): void
    {
        $this->tokenString = rtrim(
            file_get_contents(__DIR__ . '/samples/token.txt'),
            '\\r\\n'
        );

        $builder = new Builder();
        $builder->setIssuer('http://culudb-jwt-provider.dev');

        $signer = new Sha256();
        $keyString = file_get_contents(__DIR__ . '/samples/private.pem');
        $key = new Key($keyString, 'secret');

        $clock = new FrozenClock(new \DateTime('@1461829061'));

        // Test token has the same exp time as nbf time.
        // This wouldn't make sense in real-life but makes testing easier.
        $exp = 0;
        $nbf = 0;

        $this->encoderService = new DefaultJwtEncoderService(
            $builder,
            $signer,
            $key,
            $clock,
            $exp,
            $nbf
        );
    }

    /**
     * @test
     */
    public function it_encodes_claims_to_a_jwt()
    {
        $claims = [
            'uid' => 1,
            'nick' => 'foo',
            'email' => 'foo@bar.com',
        ];

        $jwt = $this->encoderService->encode($claims);

        $this->assertEquals($this->tokenString, (string) $jwt);
    }
}
