<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\User;

use ValueObjects\Exception\InvalidNativeArgumentException;

final class EmailAddress
{
    private string $value;

    public function __construct(string $value)
    {
        $filteredValue = filter_var($value, FILTER_VALIDATE_EMAIL);

        if ($filteredValue === false) {
            throw new InvalidNativeArgumentException($value, array('string (valid email address)'));
        }

        $this->value = $filteredValue;
    }

    public function toString(): string
    {
        return $this->value;
    }
}