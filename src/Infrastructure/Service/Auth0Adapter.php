<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Auth0\SDK\Auth0;
use Auth0\SDK\Exception\ApiException;
use Auth0\SDK\Exception\CoreException;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuth;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthService;
use Psr\Http\Message\ResponseInterface;

class Auth0Adapter implements AuthService
{
    /**
     * @var Auth0
     */
    private $auth0;

    public function __construct(Auth0 $auth0)
    {
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
            throw new UnSuccessfulAuth();
        } catch (CoreException $e) {
            throw new UnSuccessfulAuth();
        }
    }
}
