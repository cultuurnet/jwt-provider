<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Middleware\AllowedRefresh;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\IsAllowedRefreshToken;

final class MiddlewareServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        AllowedRefresh::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->addShared(
            AllowedRefresh::class,
            fn (): AllowedRefresh => new AllowedRefresh(
                $this->get(IsAllowedRefreshToken::class),
                $this->get(ApiKeyReaderInterface::class)
            )
        );
    }
}
