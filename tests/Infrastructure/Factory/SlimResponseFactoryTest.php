<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Factory;

use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Response;

final class SlimResponseFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_redirect_response_for_url(): void
    {
        $factory = new SlimResponseFactory();
        $url = (new UriFactory())->createUri('http://foo-bar.com');
        $response = $factory->redirectTo($url);
        $this->assertEquals(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
        $this->assertTrue($response instanceof Response);
        $this->assertEquals('http://foo-bar.com', $response->getHeaderLine('Location'));
    }
}
