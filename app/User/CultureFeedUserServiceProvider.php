<?php

namespace CultuurNet\UDB3\JwtProvider\User;

use CultuurNet\Auth\ConsumerCredentials;
use Silex\Application;
use Silex\ServiceProviderInterface;

class CultureFeedUserServiceProvider implements ServiceProviderInterface
{
    const CULTURE_FEED_USER_SERVICE = 'culture_feed_user_service';

    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
        $app[self::CULTURE_FEED_USER_SERVICE] = $app->share(
            function ($app) {
                return $this->createCultureFeedUserService($app);
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function boot(Application $app)
    {
    }

    /**
     * @param Application $app
     * @return CultureFeedUserService
     */
    private function createCultureFeedUserService(Application $app)
    {
        return new CultureFeedUserService(
            $app['culturefeed_factory']
        );
    }
}
