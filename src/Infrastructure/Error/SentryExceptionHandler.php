<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Error;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Whoops\Handler\Handler;

final class SentryExceptionHandler extends Handler
{
    /**
     * @var HubInterface
     */
    private $sentryHub;

    /**
     * @var ApiKey|null
     */
    private $apiKey;

    /**
     * @var bool
     */
    private $console;

    public function __construct(HubInterface $sentryHub, ?ApiKey $apiKey, bool $console)
    {
        $this->sentryHub = $sentryHub;
        $this->apiKey = $apiKey;
        $this->console = $console;
    }

    public function handle(): int
    {
        $this->sentryHub->configureScope(
            function (Scope $scope) {
                $scope->setTags($this->createTags($this->apiKey, $this->console));
            }
        );

        $exception = $this->getInspector()->getException();
        $this->sentryHub->captureException($exception);

        return Handler::DONE;
    }

    private function createTags(?ApiKey $apiKey, bool $console): array
    {
        return [
            'api_key' => $apiKey ? $apiKey->toString() : 'null',
            'runtime.env' => $console ? 'cli' : 'web',
        ];
    }
}
