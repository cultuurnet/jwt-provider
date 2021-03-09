<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use CultuurNet\UDB3\JwtProvider\Domain\Action\Authorize;
use CultuurNet\UDB3\JwtProvider\Domain\Action\LogOut;
use CultuurNet\UDB3\JwtProvider\Domain\Action\Refresh;
use CultuurNet\UDB3\JwtProvider\Domain\Action\RequestLogout;
use CultuurNet\UDB3\JwtProvider\Domain\Action\RequestToken;
use CultuurNet\UDB3\JwtProvider\Domain\Middleware\AllowedRefresh;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;

final class RoutingServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
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

                $router->get('/refresh', [Refresh::class, '__invoke'])
                    ->middleware($this->get(AllowedRefresh::class));

                // legacy routes
                $router->get('/culturefeed/oauth/connect', [RequestToken::class, '__invoke']);
                $router->get('/culturefeed/oauth/logout', [RequestLogout::class, '__invoke']);

                return $router;
            }
        );
    }
}
