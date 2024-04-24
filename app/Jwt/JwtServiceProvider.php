<?php

namespace CultuurNet\UDB3\JwtProvider\Jwt;

use CultuurNet\UDB3\JwtProvider\BaseServiceProvider;
use CultuurNet\UDB3\JwtProvider\Clock\SystemClock;
use DateTimeZone;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;

class JwtServiceProvider extends BaseServiceProvider
{
    protected array $provides = [
        Builder::class,
        Signer::class,
        'jwt.keys.private',
        'jwt.keys.public',
        ValidationData::class,
        JwtEncoderService::class,
        JwtDecoderService::class,
    ];

    public function provides(string $id): bool
    {
        return in_array($id, $this->provides, true);
    }

    public function register(): void
    {
        $this->addShared(
            Builder::class,
            function (): Builder {
                $builder = new Builder();
                $builder->setIssuer($this->parameter('jwt.iss'));
                return $builder;
            }
        );

        $this->addShared(
            Signer::class,
            fn(): Sha256 => new Sha256()
        );

        $this->addShared(
            'jwt.keys.private',
            function (): Key {
                $file = __DIR__ . '/../../' . $this->parameter('jwt.keys.private.file');

                return new Key(
                    'file://' . $file,
                    $this->parameter('jwt.keys.private.passphrase')
                );
            }
        );

        $this->addShared(
            'jwt.keys.public',
            function (): Key {
                $file = __DIR__ . '/../../' . $this->parameter('jwt.keys.public.file');

                return new Key(
                    'file://' . $file
                );
            }
        );

        $this->addShared(
            ValidationData::class,
            function (): ValidationData {
                $data = new ValidationData();
                $data->setIssuer($this->parameter('jwt.iss'));
                return $data;
            }
        );

        $this->addShared(
            JwtEncoderService::class,
            fn(): DefaultJwtEncoderService => new DefaultJwtEncoderService(
                $this->get(Builder::class),
                $this->get(Signer::class),
                $this->get('jwt.keys.private'),
                new SystemClock(new DateTimeZone('Europe/Brussels')),
                $this->parameter('jwt.exp'),
                $this->parameter('jwt.nbf')
            )
        );

        $this->addShared(
            JwtDecoderService::class,
            fn(): DefaultJwtDecoderService => new DefaultJwtDecoderService(
                new Parser(),
                $this->get(ValidationData::class),
                $this->get(Signer::class),
                $this->get('jwt.keys.public')
            )
        );
    }
}
