<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Auth0\SDK\Auth0;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthService;

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

    public function redirectToLogin(): void
    {
        $this->auth0->login();
    }

    //@TODO: IMPLEMENT - return real token token
    public function token(): ?string
    {
        $user = $this->auth0->getUser();
        if ($user === null) {
            return null;
        }

        return $user['token'];
    }
}
