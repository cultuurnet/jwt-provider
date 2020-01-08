<?php

namespace CultuurNet\UDB3\JwtProvider\RequestTokenStorage;

use Aura\Session\Segment;
use CultuurNet\Auth\TokenCredentials as RequestToken;

/**
 * Class RequestTokenSessionStorage
 * @package CultuurNet\UDB3\JwtProvider\RequestTokenStorage
 */
class RequestTokenSessionStorage implements RequestTokenStorageInterface
{
    private const REQUEST_TOKEN_KEY = 'RequestToken';
    
    /**
     * @var Segment
     */
    private $sessionSegment;

    public function __construct(Segment $sessionSegment)
    {
        $this->sessionSegment = $sessionSegment;
    }

    public function storeRequestToken(RequestToken $requestToken): void
    {
        $this->sessionSegment->set(
            self::REQUEST_TOKEN_KEY,
            [
                'token' => $requestToken->getToken(),
                'secret' => $requestToken->getSecret(),
            ]
        );
    }

    public function getStoredRequestToken(): ?RequestToken
    {
        $tokenData = $this->sessionSegment->get(self::REQUEST_TOKEN_KEY);

        if (!$tokenData) {
            return null;
        }

        return new RequestToken($tokenData['token'], $tokenData['secret']);
    }

    public function removeStoredRequestToken(): void
    {
        $this->sessionSegment->clear();
    }
}
