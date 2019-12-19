<?php

namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\Auth\ConsumerCredentials;
use Silex\Application;
use Silex\ServiceProviderInterface;

class OAuthServiceProvider implements ServiceProviderInterface
{
    const OAUTH_SERVICE = 'oauth_service';

    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
        $app[self::OAUTH_SERVICE] = $app->share(
            function ($app) {
                return $this->createOAuthService($app);
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
     * @return OAuthService
     */
    private function createOAuthService(Application $app)
    {
        $baseUrl = $app['config']['uitid']['base_url'];
        $consumerCredentials = $this->createConsumerCredentials($app);

        return new OAuthService(
            $baseUrl,
            $consumerCredentials
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