<?php

namespace CultuurNet\UDB3\JwtProvider\Jwt;

use CultuurNet\UDB3\JwtProvider\User\CultureFeedUserService;
use CultuurNet\UDB3\JwtProvider\User\CultureFeedUserServiceProvider;
use Silex\Application;
use Silex\ServiceProviderInterface;

class JwtOAuthCallbackHandlerServiceProvider implements ServiceProviderInterface
{
    const JWT_OAUTH_CALLBACK_HANDLER_SERVICE = 'jwt_oauth_callback_handler_service';

    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
        $app[self::JWT_OAUTH_CALLBACK_HANDLER_SERVICE] = $app->share(
            function ($app) {
                return $this->createJwtOAuthCallbackHandlerService($app);
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function boot(Application $app)
    {
    }

    private function createJwtOAuthCallbackHandlerService(Application $app)
    {
        return new JwtOAuthCallbackHandler(
            $app['jwt.encoder'],
            $app[CultureFeedUserServiceProvider::CULTURE_FEED_USER_SERVICE]
        );
    }
}