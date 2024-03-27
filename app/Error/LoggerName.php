<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Error;

final class LoggerName
{
    private string $fileNameWithoutSuffix;

    private string $loggerName;

    public function __construct(string $fileNameWithoutSuffix, ?string $customLoggerName = null)
    {
        $this->fileNameWithoutSuffix = $fileNameWithoutSuffix;
        $this->loggerName = $customLoggerName ?? 'logger.' . $this->fileNameWithoutSuffix;
    }

    public function getFileNameWithoutSuffix(): string
    {
        return $this->fileNameWithoutSuffix;
    }

    public function getLoggerName(): string
    {
        return $this->loggerName;
    }
}
