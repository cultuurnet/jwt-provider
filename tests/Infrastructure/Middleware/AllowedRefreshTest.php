<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Middleware;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerInterface;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerReadRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\InvalidApiKeyException;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\RefreshTokenNotAllowedException;
use CultuurNet\UDB3\JwtProvider\Domain\Middleware\AllowedRefresh;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ValueObjects\StringLiteral\StringLiteral;

class AllowedRefreshTest extends TestCase
{

    /**
     * @test
     */
    public function it_allows_requests_that_have_the_appropriate_group_id()
    {
        $apiKey = new ApiKey('api-key');

        $serverRequest = $this->prophesize(ServerRequestInterface::class);

        $apiKeyReader = $this->prophesize(ApiKeyReaderInterface::class);
        $apiKeyReader->read($serverRequest)->willReturn($apiKey);

        $consumerReadRepository = $this->prophesize(ConsumerReadRepositoryInterface::class);

        $consumer = $this->prophesize(ConsumerInterface::class);
        $consumerReadRepository->getConsumer($apiKey)->willReturn($consumer);

        $consumer->getPermissionGroupIds()
            ->willReturn(
                [
                    new StringLiteral('group-1'),
                    new StringLiteral('group-1'),
                    new StringLiteral('refresh-group'),
                ]
            );

        $handler = $this->prophesize(RequestHandlerInterface::class);

        $handler->handle($serverRequest)->shouldBeCalled();

        $allowedRefreshMiddleware = new AllowedRefresh(
            $consumerReadRepository->reveal(),
            $apiKeyReader->reveal(),
            'refresh-group'
        );
        $allowedRefreshMiddleware->process($serverRequest->reveal(), $handler->reveal());
    }

    /**
     * @test
     */
    public function it_throws_exception_for_request_that_do_not_have_the_appropriate_group()
    {
        $apiKey = new ApiKey('api-key');

        $serverRequest = $this->prophesize(ServerRequestInterface::class);

        $apiKeyReader = $this->prophesize(ApiKeyReaderInterface::class);
        $apiKeyReader->read($serverRequest)->willReturn($apiKey);

        $consumerReadRepository = $this->prophesize(ConsumerReadRepositoryInterface::class);

        $consumer = $this->prophesize(ConsumerInterface::class);
        $consumerReadRepository->getConsumer($apiKey)->willReturn($consumer);

        $consumer->getPermissionGroupIds()
            ->willReturn(
                [
                    new StringLiteral('group-1'),
                    new StringLiteral('group-1'),
                ]
            );

        $handler = $this->prophesize(RequestHandlerInterface::class);

        $allowedRefreshMiddleware = new AllowedRefresh(
            $consumerReadRepository->reveal(),
            $apiKeyReader->reveal(),
            'refresh-group'
        );

        $this->expectException(RefreshTokenNotAllowedException::class);

        $allowedRefreshMiddleware->process($serverRequest->reveal(), $handler->reveal());
    }

    /**
     * @test
     */
    public function it_throws_exception_for_request_that_have_invalid_consumer_credential()
    {
        $apiKey = new ApiKey('api-key');

        $serverRequest = $this->prophesize(ServerRequestInterface::class);

        $apiKeyReader = $this->prophesize(ApiKeyReaderInterface::class);
        $apiKeyReader->read($serverRequest)->willReturn($apiKey);

        $consumerReadRepository = $this->prophesize(ConsumerReadRepositoryInterface::class);

        $consumerReadRepository->getConsumer($apiKey)->willReturn(null);

        $handler = $this->prophesize(RequestHandlerInterface::class);

        $allowedRefreshMiddleware = new AllowedRefresh(
            $consumerReadRepository->reveal(),
            $apiKeyReader->reveal(),
            'refresh-group'
        );

        $this->expectException(InvalidApiKeyException::class);

        $allowedRefreshMiddleware->process($serverRequest->reveal(), $handler->reveal());
    }

    /**
     * @test
     */
    public function it_skips_request_that_do_not_contain_api_key()
    {

        $serverRequest = $this->prophesize(ServerRequestInterface::class);

        $apiKeyReader = $this->prophesize(ApiKeyReaderInterface::class);
        $apiKeyReader->read($serverRequest)->willReturn(null);

        $consumerReadRepository = $this->prophesize(ConsumerReadRepositoryInterface::class);

        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($serverRequest)->shouldBeCalled();

        $allowedRefreshMiddleware = new AllowedRefresh(
            $consumerReadRepository->reveal(),
            $apiKeyReader->reveal(),
            'refresh-group'
        );

        $allowedRefreshMiddleware->process($serverRequest->reveal(), $handler->reveal());
    }
}
