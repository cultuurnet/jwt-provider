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

    public function __construct(HubInterface $sentryHub, ?ApiKey $apiKey)
    {
        $this->sentryHub = $sentryHub;
        $this->apiKey = $apiKey;
    }

    public function handle(): int
    {
        $this->sentryHub->configureScope(
            function (Scope $scope) {
                $scope->setTags($this->createTags($this->apiKey));
            }
        );

        $exception = $this->getInspector()->getException();
        $this->sentryHub->captureException($exception);

        return Handler::DONE;
    }

    private function createTags(?ApiKey $apiKey): array
    {
        return [
            'api_key' => $apiKey ? $apiKey->toNative() : 'null',
            'runtime.env' => 'web',
        ];
    }
}
