<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use Sentry\SentrySdk;
use Sentry\State\HubInterface;
use function Sentry\init;

final class SentryServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        HubInterface::class,
    ];

    public function register(): void
    {
        $this->add(
            HubInterface::class,
            function () {
                init([
                    'dsn' => $this->parameter('sentry.dsn'),
                    'environment' => $this->parameter('sentry.environment'),
                ]);

                return SentrySdk::getCurrentHub();
            }
        );
    }
}
