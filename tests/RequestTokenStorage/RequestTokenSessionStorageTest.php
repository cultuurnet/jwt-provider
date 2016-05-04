<?php

namespace CultuurNet\UDB3\JwtProvider\RequestTokenStorage;

use CultuurNet\Auth\TokenCredentials as RequestToken;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class RequestTokenSessionStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestTokenSessionStorage
     */
    private $requestTokenSessionStorage;

    /**
     * @var RequestToken
     */
    private $requestToken;

    protected function setUp()
    {
        $this->requestTokenSessionStorage = new RequestTokenSessionStorage(
            new Session(new MockArraySessionStorage())
        );

        $this->requestToken = new RequestToken('token', 'secret');
    }

    /**
     * @test
     */
    public function it_can_store_a_request_token()
    {
        $this->requestTokenSessionStorage->storeRequestToken($this->requestToken);
        $actualRequestToken = $this->requestTokenSessionStorage->getStoredRequestToken();

        $this->assertEquals($actualRequestToken, $this->requestToken);
    }

    /**
     * @test
     */
    public function it_is_possible_to_remove_a_request_token()
    {
        $this->requestTokenSessionStorage->storeRequestToken($this->requestToken);
        $this->requestTokenSessionStorage->removeStoredRequestToken();
        $actualRequestToken = $this->requestTokenSessionStorage->getStoredRequestToken();

        $this->assertNull($actualRequestToken);
    }
}
