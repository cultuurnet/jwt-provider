<?php

namespace CultuurNet\UDB3\JwtProvider\RequestTokenStorage;

use PHPUnit\Framework\MockObject\MockObject;
use Aura\Session\Segment;
use CultuurNet\Auth\TokenCredentials as RequestToken;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class RequestTokenSessionStorageTest extends TestCase
{
    /**
     * @var Segment|PHPUnit_Framework_MockObject_MockObject
     */
    private object $sessionSegment;

    private array $sessionData;

    private RequestTokenSessionStorage $requestTokenSessionStorage;

    private RequestToken $requestToken;

    protected function setUp(): void
    {
        $this->sessionSegment = $this->createMock(Segment::class);
        $this->sessionData = [];

        $this->sessionSegment
            ->method('set')
            ->willReturnCallback(
                function ($key, $value): void {
                    $this->sessionData[$key] = $value;
                }
            );

        $this->sessionSegment
            ->method('get')
            ->willReturnCallback(
                fn($key, $alt = null) => $this->sessionData[$key] ?? $alt
            );

        $this->sessionSegment
            ->method('clear')
            ->willReturnCallback(
                function (): void {
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
