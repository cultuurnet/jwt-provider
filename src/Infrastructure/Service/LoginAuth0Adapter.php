<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Auth0\SDK\Contract\Auth0Interface;
use Auth0\SDK\Exception\NetworkException;
use Auth0\SDK\Exception\StateException;
use CultuurNet\UDB3\JwtProvider\Domain\Enum\Locale;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LoginServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

final class LoginAuth0Adapter implements LoginServiceInterface
{
    /**
     * @var Auth0Interface
     */
    private $auth0;

    public function __construct(Auth0Interface $auth0)
    {
        $this->auth0 = $auth0;
    }

    public function redirectToLogin(string $locale = Locale::DUTCH): ?ResponseInterface
    {
        $parameters = [
            'locale' => $locale,
            'referrer' => 'udb',
            'skip_verify_legacy' => 'true',
            'product_display_name' => 'UiTdatabank',
        ];
        $url = $this->auth0->login(null, $parameters);
        return (new Response())
            ->withHeader('Location', $url)
            ->withStatus(301);
    }

    public function token(): ?string
    {
        try {
            $this->auth0->exchange();
            return $this->auth0->getIdToken();
        } catch (StateException|NetworkException $e) {
            throw new UnSuccessfulAuthException();
        }
    }

    public function refreshToken(): ?string
    {
        try {
            return $this->auth0->getRefreshToken();
        } catch (\Exception $e) {
            throw new UnSuccessfulAuthException();
        }
    }
}
