<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Unit\Domain;

use Assert\InvalidArgumentException;
use CultuurNet\UDB3\JwtProvider\Domain\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{

    /**
     * @test
     */
    public function it_can_be_created_for_valid_user_string()
    {
        $targetUrl = Url::fromString('http://foo-bar.com');
        $this->assertSame('http://foo-bar.com', $targetUrl->asString());
    }

    /**
     * @test
     */
    public function it_cannot_be_created_for_invalid_url_string()
    {
        $this->expectException(InvalidArgumentException::class);
        Url::fromString('foo-bar');
    }

    /**
     * @test
     */
    public function it_can_get_and_appendix()
    {
        $url = Url::fromString('http://foo-bar.com');
        $new = $url->withAppendix('?appendix');
        $this->assertEquals('http://foo-bar.com?appendix', $new->asString());
    }
}
