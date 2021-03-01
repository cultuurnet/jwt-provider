<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use Aura\Session\Session;
use Auth0\SDK\API\Authentication;
use Auth0\SDK\Auth0;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Action\Authorize;
use CultuurNet\UDB3\JwtProvider\Domain\Action\LogOut;
use CultuurNet\UDB3\JwtProvider\Domain\Action\Refresh;
use CultuurNet\UDB3\JwtProvider\Domain\Action\RequestLogout;
use CultuurNet\UDB3\JwtProvider\Domain\Action\RequestToken;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ClientInformationRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\ExtractClientInformationFromRequest;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LoginServiceInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\GenerateAuthorizedDestinationUrl;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LogOutServiceInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\RefreshServiceInterface;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Repository\SessionClientInformation;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\ExtractLocaleFromRequest;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\IsAllowedRefreshToken;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\LoginAuth0Adapter;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\LogOutAuth0Adapter;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\RefreshAuth0Adapter;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Slim\Psr7\Factory\UriFactory;

final class ActionServiceProvider extends BaseServiceProvider
{
    // @see https://community.auth0.com/t/help-with-leeway-setting-using-auth0-php/14657
    // @see https://community.auth0.com/t/help-with-leeway-setting-using-auth0-php/14657/7
    private const JWT_IAT_LEEWAY = 30;

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
            function () {
                return new RequestToken(
                    $this->get(ExtractClientInformationFromRequest::class),
                    $this->get(LoginServiceInterface::class),
                    $this->get(ClientInformationRepositoryInterface::class),
                    $this->get(ExtractLocaleFromRequest::class)
                );
            }
        );

        $this->addShared(
            Authorize::class,
            function () {
                return new Authorize(
                    $this->get(LoginServiceInterface::class),
                    new GenerateAuthorizedDestinationUrl(),
                    $this->get(ResponseFactoryInterface::class),
                    $this->get(ClientInformationRepositoryInterface::class)
                );
            }
        );

        $this->addShared(
            RequestLogout::class,
            function () {
                return new RequestLogout(
                    $this->get(ExtractClientInformationFromRequest::class),
                    $this->get(LogOutServiceInterface::class),
                    $this->get(ClientInformationRepositoryInterface::class)
                );
            }
        );

        $this->addShared(
            LogOut::class,
            function () {
                return new LogOut(
                    $this->get(ClientInformationRepositoryInterface::class),
                    $this->get(ResponseFactoryInterface::class)
                );
            }
        );

        $this->addShared(
            Refresh::class,
            function () {
                return new Refresh(
                    $this->get(ResponseFactoryInterface::class),
                    $this->get(RefreshServiceInterface::class)
                );
            }
        );

        $this->addShared(
            LogOutServiceInterface::class,
            function () {
                return new LogOutAuth0Adapter(
                    $this->get(Auth0::class),
                    new Authentication(
                        $this->parameter('auth0.domain'),
                        $this->parameter('auth0.client_id'),
                        $this->parameter('auth0.client_secret')
                    ),
                    $this->get(ResponseFactoryInterface::class),
                    new UriFactory(),
                    $this->parameter('auth0.log_out_uri'),
                    $this->parameter('auth0.client_id')
                );
            }
        );

        $this->addShared(
            ResponseFactoryInterface::class,
            function () {
                return new SlimResponseFactory();
            }
        );

        $this->addShared(
            LoginServiceInterface::class,
            function () {
                return new LoginAuth0Adapter(
                    $this->get(Auth0::class)
                );
            }
        );

        $this->addShared(
            RefreshServiceInterface::class,
            function () {
                return new RefreshAuth0Adapter(
                    new Client(),
                    $this->parameter('auth0.client_id'),
                    $this->parameter('auth0.client_secret'),
                    $this->parameter('auth0.domain')
                );
            }
        );

        $this->addShared(
            Auth0::class,
            function () {
                JWT::$leeway = self::JWT_IAT_LEEWAY;
                return new Auth0(
                    [
                        'domain' => $this->parameter('auth0.domain'),
                        'client_id' => $this->parameter('auth0.client_id'),
                        'client_secret' => $this->parameter('auth0.client_secret'),
                        'redirect_uri' => $this->parameter('auth0.redirect_uri'),
                        'scope' => 'openid email profile offline_access',
                        'persist_id_token' => true,
                        'persist_refresh_token' => true,
                    ]
                );
            }
        );

        $this->addShared(
            IsAllowedRefreshToken::class,
            function () {
                return new IsAllowedRefreshToken(
                    $this->get(\ICultureFeed::class),
                    (string) $this->parameter('auth0.allowed_refresh_permission')
                );
            }
        );

        $this->addShared(
            ClientInformationRepositoryInterface::class,
            function () {
                $session = $this->get(Session::class);
                $segment = $session->getSegment(ClientInformationRepositoryInterface::class);
                return new SessionClientInformation(
                    $segment
                );
            }
        );

        $this->addShared(
            ExtractClientInformationFromRequest::class,
            function () {
                return new ExtractClientInformationFromRequest(
                    new UriFactory(),
                    $this->get(ApiKeyReaderInterface::class),
                    $this->get(IsAllowedRefreshToken::class)
                );
            }
        );
    }
}
