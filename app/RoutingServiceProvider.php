<?php

namespace CultuurNet\UDB3\JwtProvider;

use CultuurNet\UDB3\JwtProvider\Domain\Action\Authorize;
use CultuurNet\UDB3\JwtProvider\Domain\Action\RequestToken;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;


class RoutingServiceProvider extends BaseServiceProvider
{
    private const AUTHORIZATION_PATH = '/authorize';
    private const REQUEST_TOKEN_PATH = '/connect';

    protected $provides = [
        Router::class,
    ];

    public function register(): void
    {
        $this->add(
            Router::class,
            function () {
                $router = new Router();
                $strategy = (new ApplicationStrategy())->setContainer($this->getContainer());
                $router->setStrategy($strategy);

                $router->get(self::REQUEST_TOKEN_PATH, [RequestToken::class, '__invoke']);
                $router->get(self::AUTHORIZATION_PATH, [Authorize::class, '__invoke']);

                //@TODO: transfer other routes
//                $router->get('/register', [OAuthController::class, 'register']);
//                $router->get('/logout', [OAuthController::class, 'logout']);
//
//                 Maintain these old paths for backwards compatibility.
//                $router->get('/culturefeed/oauth/connect', [OAuthController::class, 'connect']);
//                $router->get('/culturefeed/oauth/authorize', [OAuthController::class, 'authorize']);
//                $router->get('/culturefeed/oauth/register', [OAuthController::class, 'register']);
//                $router->get('/culturefeed/oauth/logout', [OAuthController::class, 'logout']);

                return $router;
            }
        );
    }
}
