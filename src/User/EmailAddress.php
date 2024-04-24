<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\User;

use InvalidArgumentException;

final class EmailAddress
{
    private string $value;

    public function __construct(string $value)
    {
        $filteredValue = filter_var($value, FILTER_VALIDATE_EMAIL);

        if ($filteredValue === false) {
            throw new InvalidArgumentException('Invalid email address: ' . $value);
        }

        $this->value = $filteredValue;
    }

    public function toString(): string
    {
        return $this->value;
    }
}