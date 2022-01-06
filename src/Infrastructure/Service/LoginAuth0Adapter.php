<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Auth0\SDK\Auth0;
use Auth0\SDK\Exception\ApiException;
use Auth0\SDK\Exception\CoreException;
use CultuurNet\UDB3\JwtProvider\Domain\Enum\Locale;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LoginServiceInterface;
use Psr\Http\Message\ResponseInterface;

final class LoginAuth0Adapter implements LoginServiceInterface
{
    /**
     * @var Auth0
     */
    private $auth0;

    public function __construct(Auth0 $auth0)
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
        $this->auth0->login(null, null, $parameters);
        return null;
    }

    /**
     * @throws UnSuccessfulAuthException
     */
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

    /**
     * @throws UnSuccessfulAuthException
     */
    public function refreshToken(): ?string
    {
        try {
            return $this->auth0->getRefreshToken();
        } catch (ApiException $e) {
            throw new UnSuccessfulAuthException();
        } catch (CoreException $e) {
            throw new UnSuccessfulAuthException();
        }
    }
}
