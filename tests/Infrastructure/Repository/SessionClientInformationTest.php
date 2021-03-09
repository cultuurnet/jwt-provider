<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Repository;

use Aura\Session\Segment;
use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\JwtProvider\Domain\Value\ClientInformation;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\UriFactory;

final class SessionClientInformationTest extends TestCase
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


    private function aClientInformation(): ClientInformation
    {
        return new ClientInformation(
            (new UriFactory())->createUri('https://www.google.com'),
            new ApiKey('api-key'),
            true
        );
    }
}
