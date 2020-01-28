<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoDestinationPresentException;
use CultuurNet\UDB3\JwtProvider\Domain\Service\IsAllowedRefreshTokenInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\UriFactory;

class ExtractClientInformationFromRequestTest extends TestCase
{

    /**
     * @test
     */
    public function it_extracts_client_information_from_request()
    {
        $serverRequest = $this->aServerRequestWithQueryParameters(
            [
                'destination' => 'www.jwt.com'
            ]
        );

        $apiKey = $this->anApiKey();

        $apiKeyReader = $this->prophesize(ApiKeyReaderInterface::class);
        $apiKeyReader->read($serverRequest)->willReturn($apiKey);

        $isAllowedRefreshToken = $this->prophesize(IsAllowedRefreshTokenInterface::class);
        $isAllowedRefreshToken->__invoke($apiKey)->willReturn(true);

        $extract = new ExtractClientInformationFromRequest(
            new UriFactory(),
            $apiKeyReader->reveal(),
            $isAllowedRefreshToken->reveal()
        );

        $result = $extract->__invoke($serverRequest->reveal());
        $this->assertEquals($apiKey->toNative(), $result->apiKey());
        $this->assertEquals('www.jwt.com', $result->uri()->getPath());
        $this->assertTrue($result->isAllowedRefresh());
    }

    /**
     * @test
     */
    public function it_throws_exception_if_destination_query_parameter_is_missing_from_request()
    {
        $serverRequest = $this->aServerRequestWithQueryParameters([]);

        $apiKey = $this->anApiKey();

        $apiKeyReader = $this->prophesize(ApiKeyReaderInterface::class);
        $apiKeyReader->read($serverRequest)->willReturn($apiKey);

        $isAllowedRefreshToken = $this->prophesize(IsAllowedRefreshTokenInterface::class);
        $isAllowedRefreshToken->__invoke($apiKey)->willReturn(true);

        $this->expectException(NoDestinationPresentException::class);

        $extract = new ExtractClientInformationFromRequest(
            new UriFactory(),
            $apiKeyReader->reveal(),
            $isAllowedRefreshToken->reveal()
        );

        $extract->__invoke($serverRequest->reveal());
    }

    private function aServerRequestWithQueryParameters(array $queryParams)
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $serverRequest->getQueryParams()->willReturn(
            $queryParams
        );

        return $serverRequest;
    }

    /**
     * @return ApiKey
     */
    private function anApiKey(): ApiKey
    {
        return new ApiKey('api-key');
    }
}
