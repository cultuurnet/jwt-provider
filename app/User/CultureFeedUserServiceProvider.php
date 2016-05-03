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
        $cultureFeed = $this->createCultureFeed($app);

        return new CultureFeedUserService(
            $cultureFeed
        );
    }

    /**
     * @param Application $app
     * @return \CultureFeed
     */
    private function createCultureFeed(Application $app)
    {
        $oAuthClient = $this->createCultureFeedOAuthClient($app);

        return new \CultureFeed(
            $oAuthClient
        );
    }

    /**
     * @param Application $app
     * @return \CultureFeed_DefaultOAuthClient
     */
    private function createCultureFeedOAuthClient(Application $app)
    {
        $consumerCredentials = $this->createConsumerCredentials($app);

        // TODO: How to pass the user information?
        return new \CultureFeed_DefaultOAuthClient(
            $consumerCredentials->getKey(),
            $consumerCredentials->getSecret()
        );
    }

    /**
     * @param Application $app
     * @return ConsumerCredentials
     */
    private function createConsumerCredentials(Application $app)
    {
        $key = $app['config']['uitid']['consumer']['key'];
        $secret = $app['config']['uitid']['consumer']['secret'];

        return new ConsumerCredentials(
            $key,
            $secret
        );
    }
}