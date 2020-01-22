<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Repository;

use Aura\Session\Segment;
use CultuurNet\Auth\TokenCredentials as RequestToken;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\UriFactory;

class SessionTest extends TestCase
{
    /**
     * @var Segment|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionSegment;

    /**
     * @var array
     */
    private $sessionData;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var RequestToken
     */
    private $destinationUrl;

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

        $this->session = new Session(
            $this->sessionSegment,
            new UriFactory()
        );


        $this->destinationUrl = (new UriFactory())->createUri('http://foo-bar.com/');
    }

    /**
     * @test
     */
    public function it_can_store_a_request_token(): void
    {
        $this->session->storeDestinationUrl($this->destinationUrl);
        $actualRequestToken = $this->session->getDestinationUrl();

        $this->assertEquals($actualRequestToken, $this->destinationUrl);
    }

    /**
     * @test
     */
    public function it_is_possible_to_remove_a_request_token(): void
    {
        $this->session->storeDestinationUrl($this->destinationUrl);
        $this->session->removeDestinationUrl();
        $actualRequestToken = $this->session->getDestinationUrl();

        $this->assertNull($actualRequestToken);
    }
}