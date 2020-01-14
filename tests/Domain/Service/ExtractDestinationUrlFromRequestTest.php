<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoDestinationPresent;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractDestinationUrlFromRequest;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\UriFactory;

class ExtractDestinationUrlFromRequestTest extends TestCase
{

    /**
     * @test
     */
    public function it_extracts_target_url_from_request()
    {
        $serverRequest = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'http://culudb-jwt-provider.dev/connect?destination=https://culudb-silex.dev'
        );

        $extractTargetUrlFromRequestTest = new ExtractDestinationUrlFromRequest(
            new UriFactory()
        );

        $extracted = $extractTargetUrlFromRequestTest->__invoke($serverRequest);

        $this->assertEquals('https://culudb-silex.dev/', $extracted->__toString());
    }

    /**
     * @test
     */
    public function it_throws_exception_if_url_does_not_contain_destination()
    {
        $serverRequest = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'http://culudb-jwt-provider.dev/connect'
        );

        $extractTargetUrlFromRequestTest = new ExtractDestinationUrlFromRequest(
            new UriFactory()
        );

        $this->expectException(NoDestinationPresent::class);
        $extractTargetUrlFromRequestTest->__invoke($serverRequest);
    }
}
