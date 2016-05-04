<?php

namespace CultuurNet\UDB3\JwtProvider\Jwt\CultureFeed;

use CultuurNet\Auth\ConsumerCredentials;
use Silex\Application;
use Silex\ServiceProviderInterface;

class CultureFeedServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['culturefeed_factory'] = $app->share(
            function (Application $app) {
                return new CultureFeedFactory(
                    $app['culturefeed_consumer_credentials']
                );
            }
        );

        $app['culturefeed_consumer_credentials'] = $app->share(
            function (Application $app) {
                $key = $app['config']['uitid']['consumer']['key'];
                $secret = $app['config']['uitid']['consumer']['secret'];

                return new ConsumerCredentials(
                    $key,
                    $secret
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
