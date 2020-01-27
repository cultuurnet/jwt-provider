<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use CultuurNet\UDB3\JwtProvider\Domain\Middleware\AllowedRefresh;
use CultuurNet\UDB3\JwtProvider\Domain\Service\IsAllowedRefreshToken;

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
                    $this->get(IsAllowedRefreshToken::class)
                );
            }
        );
    }
}
