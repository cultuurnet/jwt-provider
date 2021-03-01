<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerInterface;
use PHPUnit\Framework\TestCase;
use ValueObjects\StringLiteral\StringLiteral;

final class IsAllowedRefreshTokenTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_true_for_api_consumer_that_has_the_appropriate_permission()
    {
        $apiKey = $this->anApiKey();

        $consumer = $this->aConsumerWithPermissionGroups(
            [
                new StringLiteral('group-31'),
                new StringLiteral('refresh-group'),
            ]
        );

        $cultureFeed = $this->prophesize(\ICultureFeed::class);
        $cultureFeed->getServiceConsumerByApiKey($apiKey)->willReturn($consumer);

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
    public function it_returns_false_for_api_consumer_that_does_not_have_the_appropriate_permission()
    {
        $apiKey = $this->anApiKey();

        $consumer = $this->aConsumerWithPermissionGroups(
            [
                new StringLiteral('group-31'),
            ]
        );

        $cultureFeed = $this->prophesize(\ICultureFeed::class);
        $cultureFeed->getServiceConsumerByApiKey($apiKey)->willReturn($consumer);

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
    public function it_returns_false_for_request_that_have_invalid_consumer_credential()
    {
        $apiKey = $this->anApiKey();

        $cultureFeed = $this->prophesize(\ICultureFeed::class);
        $cultureFeed->getServiceConsumerByApiKey($apiKey)->willReturn(null);

        $isAllowedRefreshToken = new IsAllowedRefreshToken(
            $cultureFeed->reveal(),
            'refresh-group'
        );

        $result = $isAllowedRefreshToken->__invoke($apiKey);
        $this->assertFalse($result);
    }


    private function anApiKey(): StringLiteral
    {
        return new StringLiteral('api-key');
    }

    private function aConsumerWithPermissionGroups(array $permissionGroups)
    {
        $consumer = $this->prophesize(ConsumerInterface::class);
        $consumer->getPermissionGroupIds()->willReturn(
            $permissionGroups
        );
        return $consumer;
    }
}
