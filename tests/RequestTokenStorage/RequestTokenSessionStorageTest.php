<?php

namespace CultuurNet\UDB3\JwtProvider\RequestTokenStorage;

use Aura\Session\Segment;
use CultuurNet\Auth\TokenCredentials as RequestToken;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class RequestTokenSessionStorageTest extends TestCase
{
    /**
     * @var Segment|PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionSegment;

    /**
     * @var array
     */
    private $sessionData;

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
        $this->sessionSegment = $this->createMock(Segment::class);
        $this->sessionData = [];

        $this->sessionSegment
            ->method('set')
            ->willReturnCallback(
                function ($key, $value) {
                    $this->sessionData[$key] = $value;
                }
            );

        $this->sessionSegment
            ->method('get')
            ->willReturnCallback(
                function ($key, $alt = null) {
                    return $this->sessionData[$key] ?? $alt;
                }
            );

        $this->sessionSegment
            ->method('clear')
            ->willReturnCallback(
                function () {
                    $this->sessionData = [];
                }
            );

        $this->requestTokenSessionStorage = new RequestTokenSessionStorage(
            $this->sessionSegment
        );

        $this->requestToken = new RequestToken('token', 'secret');
    }

    /**
     * @test
     */
    public function it_can_store_a_request_token(): void
    {
        $this->requestTokenSessionStorage->storeRequestToken($this->requestToken);
        $actualRequestToken = $this->requestTokenSessionStorage->getStoredRequestToken();

        $this->assertEquals($actualRequestToken, $this->requestToken);
    }

    /**
     * @test
     */
    public function it_is_possible_to_remove_a_request_token(): void
    {
        $this->requestTokenSessionStorage->storeRequestToken($this->requestToken);
        $this->requestTokenSessionStorage->removeStoredRequestToken();
        $actualRequestToken = $this->requestTokenSessionStorage->getStoredRequestToken();

        $this->assertNull($actualRequestToken);
    }
}
