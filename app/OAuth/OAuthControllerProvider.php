<?php

namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\UDB3\JwtProvider\Jwt\JwtOAuthCallbackHandlerServiceProvider;
use CultuurNet\UDB3\JwtProvider\RequestTokenStorage\RequestTokenStorageServiceProvider;
use Silex\Application;
use Silex\ControllerProviderInterface;

class OAuthControllerProvider implements ControllerProviderInterface
{
    /**
     * @inheritdoc
     */
    public function connect(Application $app)
    {
        $this->createSharedOAuthController($app);

        $controllers = $app['controllers_factory'];

        $controllers->get('/connect', 'oauth_controller:connect');
        $controllers->get('/logout', 'oauth_controller:logout')
            ->bind('uitid.oauth.logout');
        $controllers->get('/register', 'oauth_controller:register');
        $controllers->get('/authorize', 'oauth_controller:authorize')
            ->bind(OAuthUrlHelper::AUTHORISATION_ROUTE_NAME);

        return $controllers;
    }

    private function createSharedOAuthController(Application $app)
    {
        $app['oauth_controller'] = $app->share(function (Application $app) {
            return new OAuthController(
                $app[OAuthServiceProvider::OAUTH_SERVICE],
                $app[RequestTokenStorageServiceProvider::REQUEST_TOKEN_STORAGE_SERVICE],
                $app[OAuthUrlHelperServiceProvider::OAUTH_URL_HELPER_SERVICE],
                $app[JwtOAuthCallbackHandlerServiceProvider::JWT_OAUTH_CALLBACK_HANDLER_SERVICE]
            );
        });
    }
}