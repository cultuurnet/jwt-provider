<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Clock;

use DateTimeImmutable;
use DateTimeZone;

final class SystemClock implements Clock
{
    private $timezone;

    public function __construct(DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
    }

    public function getDateTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $this->timezone);
    }
}