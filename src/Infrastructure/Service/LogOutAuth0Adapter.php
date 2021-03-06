<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Auth0\SDK\API\Authentication;
use Auth0\SDK\Auth0;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LogOutServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

final class LogOutAuth0Adapter implements LogOutServiceInterface
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

    /**
     * @var string
     */
    private $clientId;


    public function __construct(
        Auth0 $auth0,
        Authentication $authentication,
        ResponseFactoryInterface $responseFactory,
        UriFactoryInterface $uriFactory,
        string $logOutUri,
        string $clientId
    ) {
        $this->responseFactory = $responseFactory;
        $this->uriFactory = $uriFactory;
        $this->authentication = $authentication;
        $this->auth0 = $auth0;
        $this->logOutUri = $logOutUri;
        $this->clientId = $clientId;
    }

    public function logout(): ?ResponseInterface
    {
        $this->auth0->logout();
        return $this->responseFactory->redirectTo($this->generateAuth0LogoutUri());
    }

    private function generateAuth0LogoutUri(): UriInterface
    {
        $destination = $this->authentication->get_logout_link($this->logOutUri, $this->clientId);
        return $this->uriFactory->createUri($destination);
    }
}
