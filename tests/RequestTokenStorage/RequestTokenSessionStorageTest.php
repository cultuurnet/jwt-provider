<?php

namespace CultuurNet\UDB3\JwtProvider\RequestTokenStorage;

use PHPUnit\Framework\MockObject\MockObject;
use Aura\Session\Segment;
use CultuurNet\Auth\TokenCredentials as RequestToken;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class RequestTokenSessionStorageTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $sessionData = [];

    private RequestTokenSessionStorage $requestTokenSessionStorage;

    private RequestToken $requestToken;

    protected function setUp(): void
    {
        $sessionSegment = $this->createMock(Segment::class);
        $this->sessionData = [];

        $sessionSegment
            ->method('set')
            ->willReturnCallback(
                function ($key, $value): void {
                    $this->sessionData[$key] = $value;
                }
            );

        $sessionSegment
            ->method('get')
            ->willReturnCallback(
                function ($key, $alt = null) {
                    return $this->sessionData[$key] ?? $alt;
                }
            );

        $sessionSegment
            ->method('clear')
            ->willReturnCallback(
                function (): void {
                    $this->sessionData = [];
                }
            );

        $this->requestTokenSessionStorage = new RequestTokenSessionStorage(
            $sessionSegment
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
