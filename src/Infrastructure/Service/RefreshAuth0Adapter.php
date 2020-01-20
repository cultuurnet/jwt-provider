<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Auth0\SDK\Auth0;
use Auth0\SDK\Exception\ApiException;
use Auth0\SDK\Exception\CoreException;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;
use CultuurNet\UDB3\JwtProvider\Domain\Service\RefreshServiceInterface;

class RefreshAuth0Adapter implements RefreshServiceInterface
{
    /**
     * @var Auth0
     */
    private $auth0;

    public function __construct(Auth0 $auth0)
    {
        $this->auth0 = $auth0;
    }

    /**
     * @inheritDoc
     */
    public function renewToken()
    {
        try {
            $this->auth0->renewTokens();
        } catch (ApiException $e) {
            throw new UnSuccessfulAuthException();
        } catch (CoreException $e) {
            throw new UnSuccessfulAuthException();
        }
    }

    public function token(): string
    {
    }
}
