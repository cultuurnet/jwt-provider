<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use PHPUnit\Framework\TestCase;
use Slim\Psr7\Uri;

class GenerateAuthorizedDestinationUrlTest extends TestCase
{
    /**
     * @test
     */
    public function it_appends_token_to_query_params_list(): void
    {
        $destinationUrl = new Uri(
            'https',
            'bar.com',
            null,
            '',
            '?query=value'
        );
        $generateAuthorizedDestinationUrlTest = new GenerateAuthorizedDestinationUrl();
        $result = $generateAuthorizedDestinationUrlTest->__invoke($destinationUrl, 'token');

        $this->assertEquals('https://bar.com/?query=value&jwt=token', $result->__toString());
    }

    /**
     * @test
     */
    public function it_adds_token_as_query_param(): void
    {
        $destinationUrl = new Uri(
            'https',
            'bar.com'
        );

        $generateAuthorizedDestinationUrlTest = new GenerateAuthorizedDestinationUrl();
        $result = $generateAuthorizedDestinationUrlTest->__invoke($destinationUrl, 'token');

        $this->assertEquals('https://bar.com/?jwt=token', $result->__toString());
    }

    /**
     * @test
     */
    public function it_includes_refresh_token_if_injected()
    {
        $destinationUrl = new Uri(
            'https',
            'bar.com',
            null,
            '',
            '?query=value'
        );
        $generateAuthorizedDestinationUrlTest = new GenerateAuthorizedDestinationUrl();
        $result = $generateAuthorizedDestinationUrlTest->__invoke($destinationUrl, 'token', 'fresh');

        $this->assertEquals('https://bar.com/?query=value&jwt=token&refresh=fresh', $result->__toString());
    }
}
