<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use Aura\Session\SessionFactory;
use Auth0\SDK\Auth0;
use CultuurNet\UDB3\JwtProvider\Domain\Action\Authorize;
use CultuurNet\UDB3\JwtProvider\Domain\Action\RequestToken;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthServiceInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractDestinationUrlFromRequest;
use CultuurNet\UDB3\JwtProvider\Domain\Service\GenerateAuthorizedDestinationUrl;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Factory\SlimResponseFactory;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Repository\Session;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\Auth0Adapter;
use Slim\Psr7\Factory\UriFactory;

class ActionServiceProvider extends BaseServiceProvider
{
    protected $provides = [
        RequestToken::class,
        Authorize::class
    ];

    public function register(): void
    {
        $this->add(
            RequestToken::class,
            function() {
                return new RequestToken(
                    new ExtractDestinationUrlFromRequest(
                        new UriFactory()
                    ),
                    $this->get(DestinationUrlRepositoryInterface::class),
                    $this->get(AuthServiceInterface::class),
                    $this->get(ResponseFactoryInterface::class)
                );
            }
        );

        $this->add(
            Authorize::class,
            function (){
                return new Authorize(
                    $this->get(AuthServiceInterface::class),
                    $this->get(DestinationUrlRepositoryInterface::class),
                    new GenerateAuthorizedDestinationUrl(),
                    $this->get(ResponseFactoryInterface::class)
                );
            }
        );

        $this->addShared(
            ResponseFactoryInterface::class,
            function (){
                return new SlimResponseFactory();
            }
        );

        $this->addShared(
            DestinationUrlRepositoryInterface::class,
            function () {
                $sessionFactory = new SessionFactory;
                $session = $sessionFactory->newInstance($_COOKIE);
                $segment = $session->getSegment(DestinationUrlRepositoryInterface::class);
                return new Session($segment, new UriFactory());
            }
        );

        $this->addShared(
            AuthServiceInterface::class,
            function () {
                return new Auth0Adapter(
                    new Auth0(
                        [
                            'domain' => $this->parameter('auth0.domain'),
                            'client_id' => $this->parameter('auth0.client_id'),
                            'client_secret' => $this->parameter('auth0.client_secret'),
                            'redirect_uri' => $this->parameter('auth0.redirect_uri'),
                            'scope' => 'openid email profile',
                            'persist_id_token' => true,
                        ]
                    )
                );
            }
        );
    }
}
