<?php

namespace CultuurNet\UDB3\JwtProvider;

use CultuurNet\UDB3\JwtProvider\OAuth\OAuthController;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;

class RoutingServiceProvider extends BaseServiceProvider
{
    public const AUTHORIZATION_PATH = '/authorize';

    protected array $provides = [
        Router::class,
    ];

    public function provides(string $id): bool
    {
        return in_array($id, $this->provides, true);
    }

    public function register(): void
    {
        $this->add(
            Router::class,
            function (): Router {
                $router = new Router();
                $strategy = (new ApplicationStrategy())->setContainer($this->getContainer());
                $router->setStrategy($strategy);

                $router->get('/connect', [OAuthController::class, 'connect']);
                $router->get('/register', [OAuthController::class, 'register']);
                $router->get('/logout', [OAuthController::class, 'logout']);
                $router->get(self::AUTHORIZATION_PATH, [OAuthController::class, 'authorize']);

                // Maintain these old paths for backwards compatibility.
                $router->get('/culturefeed/oauth/connect', [OAuthController::class, 'connect']);
                $router->get('/culturefeed/oauth/authorize', [OAuthController::class, 'authorize']);
                $router->get('/culturefeed/oauth/register', [OAuthController::class, 'register']);
                $router->get('/culturefeed/oauth/logout', [OAuthController::class, 'logout']);

                return $router;
            }
        );
    }
}
