<?php

namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\UDB3\JwtProvider\BaseServiceProvider;
use CultuurNet\UDB3\JwtProvider\Jwt\JwtOAuthCallbackHandler;
use CultuurNet\UDB3\JwtProvider\RoutingServiceProvider;

class OAuthServiceProvider extends BaseServiceProvider
{
    protected $provides = [
        OAuthService::class,
        OAuthCallbackHandlerInterface::class,
        OAuthUrlHelper::class,
    ];

    public function register(): void
    {
        $this->addShared(
            OAuthService::class,
            function (): OAuthService {
                $baseUrl = $this->parameter('uitid.base_url');
                $consumerCredentials = $this->get(ConsumerCredentials::class);

                return new OAuthService(
                    $baseUrl,
                    $consumerCredentials
                );
            }
        );

        $this->addShared(
            OAuthCallbackHandlerInterface::class,
            fn() => $this->get(JwtOAuthCallbackHandler::class)
        );

        $this->addShared(
            OAuthUrlHelper::class,
            fn(): OAuthUrlHelper => new OAuthUrlHelper(RoutingServiceProvider::AUTHORIZATION_PATH)
        );
    }
}
