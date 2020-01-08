<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Unit\Domain;

use Assert\InvalidArgumentException;
use CultuurNet\UDB3\JwtProvider\Domain\DestinationUrl;
use PHPUnit\Framework\TestCase;

class DestinationUrlTest extends TestCase
{

    /**
     * @test
     */
    public function it_can_be_created_for_valid_user_string()
    {
        $targetUrl = DestinationUrl::fromString('http://foo-bar.com');
        $this->assertSame('http://foo-bar.com',$targetUrl->asString());
    }

    /**
     * @test
     */
    public function it_cannot_be_created_for_invalid_url_string()
    {
        $this->expectException(InvalidArgumentException::class);
        DestinationUrl::fromString('foo-bar');
    }
}
