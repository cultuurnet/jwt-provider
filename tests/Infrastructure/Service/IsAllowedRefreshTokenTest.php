<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\ApiGuard\Consumer\InMemoryConsumerRepository;
use CultuurNet\UDB3\JwtProvider\MockConsumer;
use PHPUnit\Framework\TestCase;

final class IsAllowedRefreshTokenTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_true_for_api_consumer_that_has_the_appropriate_permission(): void
    {
        $apiKey = new ApiKey('c90b08ea-b223-4428-9b5f-05a36bde3f1a');
        $consumer = (new MockConsumer($apiKey))
            ->withPermissionGroupIds(['group-31', 'refresh-group']);

        $repository = new InMemoryConsumerRepository();
        $repository->setConsumer($apiKey, $consumer);

        $isAllowedRefreshToken = new IsAllowedRefreshToken($repository, 'refresh-group');

        $result = $isAllowedRefreshToken->__invoke($apiKey);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function it_returns_false_for_api_consumer_that_does_not_have_the_appropriate_permission(): void
    {
        $apiKey = new ApiKey('c90b08ea-b223-4428-9b5f-05a36bde3f1a');
        $consumer = (new MockConsumer($apiKey))
            ->withPermissionGroupIds(['group-31']);

        $repository = new InMemoryConsumerRepository();
        $repository->setConsumer($apiKey, $consumer);

        $isAllowedRefreshToken = new IsAllowedRefreshToken($repository, 'refresh-group');

        $result = $isAllowedRefreshToken->__invoke($apiKey);
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function it_returns_false_for_request_that_have_invalid_consumer_credential(): void
    {
        $apiKey = new ApiKey('c90b08ea-b223-4428-9b5f-05a36bde3f1a');

        $repository = new InMemoryConsumerRepository();

        $isAllowedRefreshToken = new IsAllowedRefreshToken($repository, 'refresh-group');

        $result = $isAllowedRefreshToken->__invoke($apiKey);
        $this->assertFalse($result);
    }
}
