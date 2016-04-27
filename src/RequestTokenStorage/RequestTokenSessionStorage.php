<?php

namespace CultuurNet\UDB3\JwtProvider\RequestTokenStorage;

use CultuurNet\Auth\TokenCredentials as RequestToken;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class RequestTokenSessionStorage
 * @package CultuurNet\UDB3\JwtProvider\RequestTokenStorage
 */
class RequestTokenSessionStorage implements RequestTokenStorageInterface
{
    const REQUEST_TOKEN = 'RequestToken';
    
    /**
     * @var SessionInterface
     */
    private $session;
    
    /**
     * RequestTokenSessionStorage constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param RequestToken $requestToken
     */
    public function storeRequestToken(RequestToken $requestToken)
    {
        $this->session->set(self::REQUEST_TOKEN, $requestToken);
    }

    /**
     * @return RequestToken|null
     */
    public function getStoredRequestToken()
    {
        return $this->session->get(self::REQUEST_TOKEN);
    }

    public function removeStoredRequestToken()
    {
        $this->session->remove(self::REQUEST_TOKEN);
    }
}
