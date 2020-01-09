<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain;

use Assert\Assertion;

class Url
{

    /**
     * @var string
     */
    private $value;

    private function __construct()
    {
    }

    public static function fromString(string $value): Url
    {
        $instance = new Url();
        Assertion::url($value);
        $instance->value = $value;
        return $instance;
    }

    public function asString(): string
    {
        return $this->value;
    }

    public function withAppendix(string $appendix): Url
    {
        return self::fromString($this->value . $appendix);
    }
}
