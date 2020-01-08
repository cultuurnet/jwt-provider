<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Unit\Domain\Service;

use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractDestinationUrlFromRequest;
use CultuurNet\UDB3\JwtProvider\Domain\DestinationUrl;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;

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

        $extractTargetUrlFromRequestTest = new ExtractDestinationUrlFromRequest();
        $extracted = $extractTargetUrlFromRequestTest->__invoke($serverRequest);

        $this->assertEquals($extracted, DestinationUrl::fromString('https://culudb-silex.dev'));
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

        $extractTargetUrlFromRequestTest = new ExtractDestinationUrlFromRequest();

        $this->expectException(\InvalidArgumentException::class);
        $extractTargetUrlFromRequestTest->__invoke($serverRequest);
    }

}
