<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerInterface;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerReadRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\InvalidApiKeyException;
use PHPUnit\Framework\TestCase;
use ValueObjects\StringLiteral\StringLiteral;

class IsAllowedRefreshTokenTest extends TestCase
{
    /**
     * @test
     * @throws InvalidApiKeyException
     */
    public function it_returns_true_for_api_consumer_that_has_the_appropriate_permission()
    {
        $apiKey = new ApiKey('api-key');

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

        $isAllowedRefreshToken = new IsAllowedRefreshToken(
            $consumerReadRepository->reveal(),
            'refresh-group'
        );

        $result = $isAllowedRefreshToken->__invoke($apiKey);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function it_returns_false_for_api_consumer_that_does_not_have_the_appropriate_permission()
    {
        $apiKey = new ApiKey('api-key');

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

        $isAllowedRefreshToken = new IsAllowedRefreshToken(
            $consumerReadRepository->reveal(),
            'refresh-group'
        );

        $result = $isAllowedRefreshToken->__invoke($apiKey);
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function it_throws_exception_for_request_that_have_invalid_consumer_credential()
    {
        $apiKey = new ApiKey('api-key');

        $consumerReadRepository = $this->prophesize(ConsumerReadRepositoryInterface::class);

        $consumerReadRepository->getConsumer($apiKey)->willReturn(null);

        $isAllowedRefreshToken = new IsAllowedRefreshToken(
            $consumerReadRepository->reveal(),
            'refresh-group'
        );


        $this->expectException(InvalidApiKeyException::class);
        $isAllowedRefreshToken->__invoke($apiKey);
    }
}
