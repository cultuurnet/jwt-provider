<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Auth0\SDK\API\Authentication;
use Auth0\SDK\Auth0;
use Auth0\SDK\Exception\ApiException;
use Auth0\SDK\Exception\CoreException;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LoginServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

class LoginAuth0Adapter implements LoginServiceInterface
{
    /**
     * @var Auth0
     */
    private $auth0;

    /**
     * @var Authentication
     */
    private $authentication;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;

    public function __construct(
        Auth0 $auth0
    ) {
        $this->auth0 = $auth0;
    }

    public function redirectToLogin(): ?ResponseInterface
    {
        $this->auth0->login();
        return null;
    }

    public function token(): ?string
    {
        try {
            return $this->auth0->getIdToken();
        } catch (ApiException $e) {
            throw new UnSuccessfulAuthException();
        } catch (CoreException $e) {
            throw new UnSuccessfulAuthException();
        }
    }

    public function logout(string $logOutUri): ?ResponseInterface
    {
        $this->auth0->logout();
        return $this->responseFactory->redirectTo($this->generateAuth0LogoutUri($logOutUri));
    }

    private function generateAuth0LogoutUri(string $logOutUri): UriInterface
    {
        $destination = $this->authentication->get_logout_link($logOutUri);
        return $this->uriFactory->createUri($destination);
    }
}
