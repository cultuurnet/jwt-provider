<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain;

use Assert\Assertion;

class DestinationUrl
{

    /**
     * @var string
     */
    private $value;

    private function __construct()
    {
    }

    public static function fromString(string $value): DestinationUrl
    {
        $instance = new DestinationUrl();
        Assertion::url($value);
        $instance->value = $value;
        return $instance;
    }

    public function asString(): string
    {
        return $this->value;
    }
}
