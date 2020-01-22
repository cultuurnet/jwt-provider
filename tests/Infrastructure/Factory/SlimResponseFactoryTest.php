<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Factory;

use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Response;

class SlimResponseFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_bad_request_response_with_message()
    {
        $factory = new SlimResponseFactory();
        $response = $factory->badRequestWithMessage('Message');
        $this->assertEquals('Message', $response->getBody());
        $this->assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        $this->assertTrue($response instanceof Response);
    }

    /**
     * @test
     */
    public function it_creates_bad_request_response()
    {
        $factory = new SlimResponseFactory();
        $response = $factory->badRequest();
        $this->assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        $this->assertTrue($response instanceof Response);
    }

    /**
     * @test
     */
    public function it_creates_redirect_response_for_url()
    {
        $factory = new SlimResponseFactory();
        $url = (new UriFactory())->createUri('http://foo-bar.com/');
        $response = $factory->redirectTo($url);
        $this->assertEquals(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
        $this->assertTrue($response instanceof Response);
        $this->assertEquals('http://foo-bar.com/', $response->getHeaderLine('Location'));
    }
}