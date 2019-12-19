<?php

namespace CultuurNet\UDB3\JwtProvider\RequestTokenStorage;

use Silex\Application;
use Silex\ServiceProviderInterface;

class RequestTokenStorageServiceProvider implements ServiceProviderInterface
{
    const REQUEST_TOKEN_STORAGE_SERVICE = 'request_token_storage_service';

    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
        $app[self::REQUEST_TOKEN_STORAGE_SERVICE] = $app->share(
            function ($app) {
                return $this->createRequestTokenStorageService($app);
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
     * @return RequestTokenSessionStorage
     */
    private function createRequestTokenStorageService(Application $app)
    {
        return new RequestTokenSessionStorage(
            $app['session']
        );
    }
}