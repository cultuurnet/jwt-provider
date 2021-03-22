<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Error;

use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKeyAuthenticationException;
use CultuurNet\UDB3\ApiGuard\Request\RequestAuthenticationException;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\JwtProviderExceptionInterface;
use League\Route\Http\Exception\MethodNotAllowedException;
use League\Route\Http\Exception\NotFoundException;
use Psr\Log\LoggerInterface;
use Whoops\Handler\Handler;

final class ErrorLoggerHandler extends Handler
{
    private const BAD_REQUEST_EXCEPTIONS = [
        ApiKeyAuthenticationException::class,
        RequestAuthenticationException::class,
        NotFoundException::class,
        MethodNotAllowedException::class,
        JwtProviderExceptionInterface::class,
    ];

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(): ?int
    {
        $throwable = $this->getInspector()->getException();

        // Don't log exceptions that are caused by user errors
        if (in_array(get_class($throwable), self::BAD_REQUEST_EXCEPTIONS)) {
            return null;
        }

        // Include the original throwable as "exception" so that the Sentry monolog handler can process it correctly.
        $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);

        return null;
    }
}
