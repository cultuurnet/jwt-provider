<?php

namespace CultuurNet\UDB3\JwtProvider\Jwt;

use CultuurNet\UDB3\Jwt\JwtDecoderService;
use CultuurNet\UDB3\Jwt\JwtEncoderService;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;
use Silex\Application;
use Silex\ServiceProviderInterface;
use ValueObjects\Number\Integer;

class JwtServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['jwt.builder'] = $app->share(
            function (Application $app) {
                $builder = new Builder();
                $builder->setIssuer($app['config']['jwt']['iss']);
                return $builder;
            }
        );

        $app['jwt.signer'] = $app->share(
            function () {
                return new Sha256();
            }
        );

        $app['jwt.keys.private'] = $app->share(
            function (Application $app) {
                $file = __DIR__ . '/../../' . $app['config']['jwt']['keys']['private']['file'];

                return new Key(
                    'file://' . $file,
                    $app['config']['jwt']['keys']['private']['passphrase']
                );
            }
        );

        $app['jwt.keys.public'] = $app->share(
            function (Application $app) {
                $file = __DIR__ . '/../../' . $app['config']['jwt']['keys']['public']['file'];

                return new Key(
                    'file://' . $file
                );
            }
        );

        $app['jwt.validation_data'] = $app->share(
            function (Application $app) {
                $data = new ValidationData();
                $data->setIssuer($app['config']['jwt']['iss']);
                return $data;
            }
        );

        $app['jwt.encoder'] = $app->share(
            function (Application $app) {
                return new JwtEncoderService(
                    $app['jwt.builder'],
                    $app['jwt.signer'],
                    $app['jwt.keys.private'],
                    $app['clock'],
                    new Integer($app['config']['jwt']['exp']),
                    new Integer($app['config']['jwt']['nbf'])
                );
            }
        );

        $app['jwt.decoder'] = $app->share(
            function (Application $app) {
                return new JwtDecoderService(
                    new Parser(),
                    $app['jwt.validation_data'],
                    $app['jwt.signer'],
                    $app['jwt.keys.public']
                );
            }
        );
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
