<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use Aura\Session\Session;
use Auth0\SDK\API\Authentication;
use Auth0\SDK\Auth0;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerReadRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Action\Authorize;
use CultuurNet\UDB3\JwtProvider\Domain\Action\LogOut;
use CultuurNet\UDB3\JwtProvider\Domain\Action\Refresh;
use CultuurNet\UDB3\JwtProvider\Domain\Action\RequestLogout;
use CultuurNet\UDB3\JwtProvider\Domain\Action\RequestToken;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ClientInformationRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\GenerateAuthorizedDestinationUrl;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LoginServiceInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LogOutServiceInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\RefreshServiceInterface;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Repository\SessionClientInformation;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\ExtractClientInformationFromRequest;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\ExtractLocaleFromRequest;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\IsAllowedRefreshToken;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\LoginAuth0Adapter;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\LogOutAuth0Adapter;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\RefreshAuth0Adapter;
use GuzzleHttp\Client;
use Slim\Psr7\Factory\UriFactory;

final class ActionServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        RequestToken::class,
        Authorize::class,
        RequestLogout::class,
        LogOut::class,
        Refresh::class,
        IsAllowedRefreshToken::class,
    ];

    public function register(): void
    {
        $this->addShared(
            RequestToken::class,
            fn (): RequestToken => new RequestToken(
                $this->get(ExtractClientInformationFromRequest::class),
                $this->get(LoginServiceInterface::class),
                $this->get(ClientInformationRepositoryInterface::class),
                $this->get(ExtractLocaleFromRequest::class)
            )
        );

        $this->addShared(
            Authorize::class,
            fn (): Authorize => new Authorize(
                $this->get(LoginServiceInterface::class),
                new GenerateAuthorizedDestinationUrl(),
                $this->get(ResponseFactoryInterface::class),
                $this->get(ClientInformationRepositoryInterface::class)
            )
        );

        $this->addShared(
            RequestLogout::class,
            fn (): RequestLogout => new RequestLogout(
                $this->get(ExtractClientInformationFromRequest::class),
                $this->get(LogOutServiceInterface::class),
                $this->get(ClientInformationRepositoryInterface::class)
            )
        );

        $this->addShared(
            LogOut::class,
            fn (): LogOut => new LogOut(
                $this->get(ClientInformationRepositoryInterface::class),
                $this->get(ResponseFactoryInterface::class)
            )
        );

        $this->addShared(
            Refresh::class,
            fn (): Refresh => new Refresh(
                $this->get(ResponseFactoryInterface::class),
                $this->get(RefreshServiceInterface::class)
            )
        );

        $this->addShared(
            LogOutServiceInterface::class,
            fn (): LogOutAuth0Adapter => new LogOutAuth0Adapter(
                $this->get(Auth0::class),
                new Authentication(
                    [
                        'domain' => $this->parameter('keycloak.domain'),
                        'clientId' => $this->parameter('keycloak.client_id'),
                        'clientSecret' => $this->parameter('keycloak.client_secret'),
                        'cookieSecret' => $this->parameter('keycloak.cookie_secret'),
                    ]
                ),
                $this->get(ResponseFactoryInterface::class),
                new UriFactory(),
                $this->parameter('keycloak.log_out_uri'),
                $this->parameter('keycloak.client_id')
            )
        );

        $this->addShared(
            ResponseFactoryInterface::class,
            fn (): SlimResponseFactory => new SlimResponseFactory()
        );

        $this->addShared(
            LoginServiceInterface::class,
            fn (): LoginAuth0Adapter => new LoginAuth0Adapter(
                $this->get(Auth0::class)
            )
        );

        $this->addShared(
            RefreshServiceInterface::class,
            fn (): RefreshAuth0Adapter => new RefreshAuth0Adapter(
                new Client(),
                $this->parameter('keycloak.client_id'),
                $this->parameter('keycloak.client_secret'),
                $this->parameter('keycloak.domain')
            )
        );

        $this->addShared(
            Auth0::class,
            fn (): Auth0 => new Auth0(
                [
                    'domain' => $this->parameter('keycloak.domain'),
                    'clientId' => $this->parameter('keycloak.client_id'),
                    'clientSecret' => $this->parameter('keycloak.client_secret'),
                    'redirectUri' => $this->parameter('keycloak.redirect_uri'),
                    'scope' => ['openid','email','profile','offline_access'],
                    'persistIdToken' => true,
                    'persistRefreshToken' => true,
                    'tokenLeeway' => $this->parameter('keycloak.id_token_leeway'),
                    'cookieSecret' => $this->parameter('keycloak.cookie_secret'),
                ]
            )
        );

        $this->addShared(
            IsAllowedRefreshToken::class,
            fn (): IsAllowedRefreshToken => new IsAllowedRefreshToken(
                $this->get(ConsumerReadRepositoryInterface::class),
                (string)$this->parameter('keycloak.allowed_refresh_permission')
            )
        );

        $this->addShared(
            ClientInformationRepositoryInterface::class,
            function (): SessionClientInformation {
                $session = $this->get(Session::class);
                $segment = $session->getSegment(ClientInformationRepositoryInterface::class);
                return new SessionClientInformation(
                    $segment
                );
            }
        );

        $this->addShared(
            ExtractClientInformationFromRequest::class,
            fn (): ExtractClientInformationFromRequest => new ExtractClientInformationFromRequest(
                new UriFactory(),
                $this->get(ApiKeyReaderInterface::class),
                $this->get(IsAllowedRefreshToken::class)
            )
        );
    }
}
