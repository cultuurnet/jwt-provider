<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerReadRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Middleware\AllowedRefresh;

class MiddlewareServiceProvider extends BaseServiceProvider
{
    protected $provides = [
        AllowedRefresh::class,
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->add(
            AllowedRefresh::class,
            function () {
                return new AllowedRefresh(
                    $this->get(ConsumerReadRepositoryInterface::class),
                    $this->get(ApiKeyReaderInterface::class),
                    (string) $this->parameter('auth0.allowed_refresh_permission')
                );
            }
        );
    }
}
