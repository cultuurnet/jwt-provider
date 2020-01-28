<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Repository;

use Aura\Session\Segment;
use CultuurNet\UDB3\JwtProvider\Domain\Value\ClientInformation;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\UriFactory;
use ValueObjects\StringLiteral\StringLiteral;

class SessionClientInformationTest extends TestCase
{
    /**
     * @test
     */
    public function it_stores_client_information(): void
    {
        $sessionSegment = $this->createMock(Segment::class);

        $session = new SessionClientInformation(
            $sessionSegment
        );

        $clientInformation = $this->aClientInformation();

        $session->store($clientInformation);
        $result = $session->get();
        $this->assertEquals($result, $result);
    }

    /**
     * @test
     */
    public function it_removes_client_information(): void
    {
        $sessionSegment = $this->createMock(Segment::class);

        $session = new SessionClientInformation(
            $sessionSegment
        );

        $clientInformation = $this->aClientInformation();

        $session->store($clientInformation);

        $session->clear();

        $result = $session->get();
        $this->assertNull($result);
    }

    /**
     * @return ClientInformation
     */
    private function aClientInformation(): ClientInformation
    {
        return new ClientInformation(
            (new UriFactory())->createUri('https://www.google.com'),
            new StringLiteral('api-key'),
            true
        );
    }
}
