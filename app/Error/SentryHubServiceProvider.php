<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Error;

use CultuurNet\UDB3\JwtProvider\BaseServiceProvider;
use Sentry\SentrySdk;
use Sentry\State\HubInterface;
use function Sentry\init;

final class SentryHubServiceProvider extends BaseServiceProvider
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
            function (): \Sentry\State\HubInterface {
                init([
                    'dsn' => $this->parameter('sentry.dsn'),
                    'environment' => $this->parameter('sentry.environment'),
                ]);

                return SentrySdk::getCurrentHub();
            }
        );
    }
}
