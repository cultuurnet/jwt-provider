<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Middleware\AllowedRefresh;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\IsAllowedRefreshToken;

final class MiddlewareServiceProvider extends BaseServiceProvider
{
    protected $provides = [
        AllowedRefresh::class,
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->addShared(
            AllowedRefresh::class,
            function () {
                return new AllowedRefresh(
                    $this->get(IsAllowedRefreshToken::class),
                    $this->get(ApiKeyReaderInterface::class)
                );
            }
        );
    }
}
