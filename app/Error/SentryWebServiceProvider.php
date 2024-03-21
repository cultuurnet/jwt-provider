<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Error;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\JwtProvider\BaseServiceProvider;
use Monolog\Logger;
use Sentry\Monolog\Handler as SentryHandler;
use Sentry\State\HubInterface;

final class SentryWebServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        SentryHandlerScopeDecorator::class,
    ];

    public function register(): void
    {
        $this->addShared(
            SentryHandlerScopeDecorator::class,
            fn (): \CultuurNet\UDB3\JwtProvider\Error\SentryHandlerScopeDecorator => SentryHandlerScopeDecorator::forWeb(
                new SentryHandler($this->get(HubInterface::class), Logger::ERROR),
                $this->get(ApiKey::class)
            )
        );
    }
}
