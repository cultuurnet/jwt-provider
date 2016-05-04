<?php

namespace CultuurNet\UDB3\JwtProvider\OAuth;

use Silex\Application;
use Silex\ServiceProviderInterface;

class OAuthUrlHelperServiceProvider implements ServiceProviderInterface
{
    const OAUTH_URL_HELPER_SERVICE = 'oauth_url_helper_service';

    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
        $app[self::OAUTH_URL_HELPER_SERVICE] = $app->share(
            function ($app) {
                return $this->createOAuthUrlHelperService($app);
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
     * @return OAuthUrlHelper
     */
    private function createOAuthUrlHelperService(Application $app)
    {
        return new OAuthUrlHelper($app['url_generator']);
    }
}