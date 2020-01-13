<?php

namespace CultuurNet\UDB3\JwtProvider;

use CultuurNet\UDB3\Jwt\JwtDecoderService;
use CultuurNet\UDB3\Jwt\JwtDecoderServiceInterface;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;

class JwtServiceProvider extends BaseServiceProvider
{
    protected $provides = [
        JwtDecoderServiceInterface::class,
    ];

    public function register(): void
    {
        $this->addShared(
            Builder::class,
            function () {
                $builder = new Builder();
                $builder->setIssuer($this->parameter('jwt.iss'));
                return $builder;
            }
        );

        $this->addShared(
            Signer::class,
            function () {
                return new Sha256();
            }
        );

        $this->addShared(
            'jwt.keys.public',
            function () {
                $file = __DIR__ . '/../' . $this->parameter('jwt.keys.public.file');

                return new Key(
                    'file://' . $file
                );
            }
        );

        $this->addShared(
            ValidationData::class,
            function () {
                $data = new ValidationData();
                $data->setIssuer($this->parameter('jwt.iss'));
                return $data;
            }
        );

        $this->addShared(
            JwtDecoderServiceInterface::class,
            function () {
                return new JwtDecoderService(
                    new Parser(),
                    $this->get(ValidationData::class),
                    $this->get(Signer::class),
                    $this->get('jwt.keys.public')
                );
            }
        );
    }
}
