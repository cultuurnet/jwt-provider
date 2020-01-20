<?php

namespace CultuurNet\UDB3\JwtProvider;

use CultuurNet\UDB3\JwtProvider\Domain\Action\Authorize;
use CultuurNet\UDB3\JwtProvider\Domain\Action\LogOut;
use CultuurNet\UDB3\JwtProvider\Domain\Action\Refresh;
use CultuurNet\UDB3\JwtProvider\Domain\Action\RequestLogout;
use CultuurNet\UDB3\JwtProvider\Domain\Action\RequestToken;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;

class RoutingServiceProvider extends BaseServiceProvider
{

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

                $router->get('/connect', [RequestToken::class, '__invoke']);
                $router->get('/authorize', [Authorize::class, '__invoke']);

                $router->get('/logout', [RequestLogout::class, '__invoke']);
                $router->get('/logout-confirm', [LogOut::class, '__invoke']);

                $router->get('/refresh', [Refresh::class, '__invoke']);


                // Maintain these old paths for backwards compatibility.
                $router->get('/culturefeed/oauth/connect', [RequestToken::class, '__invoke']);
                $router->get('/culturefeed/oauth/authorize', [Authorize::class, '__invoke']);
                $router->get('/culturefeed/oauth/logout', [RequestLogout::class, '__invoke']);
                $router->get('/culturefeed/oauth/logout-confirm', [LogOut::class, '__invoke']);

                return $router;
            }
        );
    }
}
