<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class IsAllowedRefreshTokenTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_true_for_api_consumer_that_has_the_appropriate_permission(): void
    {
        $apiKey = $this->anApiKey();

        $consumer = $this->aConsumerWithPermissionGroups(
            [
                'group-31',
                'refresh-group',
            ]
        );

        $cultureFeed = $this->prophesize(\ICultureFeed::class);
        $cultureFeed->getServiceConsumerByApiKey($apiKey->toString())->willReturn($consumer);

        $isAllowedRefreshToken = new IsAllowedRefreshToken(
            $cultureFeed->reveal(),
            'refresh-group'
        );

        $result = $isAllowedRefreshToken->__invoke($apiKey);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function it_returns_false_for_api_consumer_that_does_not_have_the_appropriate_permission(): void
    {
        $apiKey = $this->anApiKey();

        $consumer = $this->aConsumerWithPermissionGroups(
            [
                'group-31',
            ]
        );

        $cultureFeed = $this->prophesize(\ICultureFeed::class);
        $cultureFeed->getServiceConsumerByApiKey($apiKey->toString())->willReturn($consumer);

        $isAllowedRefreshToken = new IsAllowedRefreshToken(
            $cultureFeed->reveal(),
            'refresh-group'
        );

        $result = $isAllowedRefreshToken->__invoke($apiKey);
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function it_returns_false_for_request_that_have_invalid_consumer_credential(): void
    {
        $apiKey = $this->anApiKey();

        $cultureFeed = $this->prophesize(\ICultureFeed::class);
        $cultureFeed->getServiceConsumerByApiKey($apiKey->toString())->willReturn(null);

        $isAllowedRefreshToken = new IsAllowedRefreshToken(
            $cultureFeed->reveal(),
            'refresh-group'
        );

        $result = $isAllowedRefreshToken->__invoke($apiKey);
        $this->assertFalse($result);
    }


    private function anApiKey(): ApiKey
    {
        return new ApiKey('api-key');
    }

    /**
     * @param string[] $permissionGroups
     * @return ObjectProphecy<ConsumerInterface>
     */
    private function aConsumerWithPermissionGroups(array $permissionGroups)
    {
        $consumer = $this->prophesize(ConsumerInterface::class);
        $consumer->getPermissionGroupIds()->willReturn(
            $permissionGroups
        );
        return $consumer;
    }
}
