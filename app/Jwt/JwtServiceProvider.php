<?php

namespace CultuurNet\UDB3\JwtProvider\Jwt;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Silex\Application;
use Silex\ServiceProviderInterface;

class JwtServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['jwt.builder'] = $app->share(
            function () {
                return new Builder();
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
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
