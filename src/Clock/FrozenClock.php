<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Clock;

use DateTimeInterface;

final class FrozenClock implements Clock
{
    private DateTimeInterface $time;

    public function __construct(DateTimeInterface $dateTime)
    {
        $this->setTime($dateTime);
    }

    protected function setTime(DateTimeInterface $dateTime) {
        $this->time = $dateTime;
    }

    public function getDateTime():DateTimeInterface
    {
        return $this->time;
    }
}