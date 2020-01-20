<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Auth0\SDK\API\Authentication;
use Auth0\SDK\Auth0;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LogOutServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

class LogOutAuth0Adapter implements LogOutServiceInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;
    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;

    /**
     * @var string
     */
    private $logOutUri;
    /**
     * @var Authentication
     */
    private $authentication;
    /**
     * @var Auth0
     */
    private $auth0;


    public function __construct(
        Auth0 $auth0,
        Authentication $authentication,
        ResponseFactoryInterface $responseFactory,
        UriFactoryInterface $uriFactory,
        string $logOutUri
    ) {
        $this->responseFactory = $responseFactory;
        $this->uriFactory = $uriFactory;
        $this->logOutUri = $logOutUri;
        $this->authentication = $authentication;
        $this->auth0 = $auth0;
    }

    public function logout(): ?ResponseInterface
    {
        $this->auth0->logout();
        return $this->responseFactory->redirectTo($this->generateAuth0LogoutUri($this->logOutUri));
    }

    private function generateAuth0LogoutUri(): UriInterface
    {
        $destination = $this->authentication->get_logout_link($this->logOutUri);
        return $this->uriFactory->createUri($destination);
    }
}
