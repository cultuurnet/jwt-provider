<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultuurNet\UDB3\JwtProvider\Domain\Enum\Locale;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\ExtractLocaleFromRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ExtractLocaleFromRequestTest extends TestCase
{
    /**
     * @test
     * @dataProvider validLocalesProvider
     * @param string $locale
     */
    public function it_return_locale_for_request_having_a_valid_locale(string $locale)
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $serverRequest->getQueryParams()
            ->willReturn(
                [
                    'lang' => $locale,
                ]
            );

        $extractLocaleFromRequest = new ExtractLocaleFromRequest();
        $result = $extractLocaleFromRequest->__invoke($serverRequest->reveal());
        $this->assertEquals($result, $locale);
    }

    /**
     * @test
     */
    public function it_returns_default_dutch_for_requests_with_no_locale()
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $serverRequest->getQueryParams()->willReturn([]);

        $extractLocaleFromRequest = new ExtractLocaleFromRequest();
        $result = $extractLocaleFromRequest->__invoke($serverRequest->reveal());
        $this->assertEquals($result, Locale::DUTCH);
    }

    /**
     * @test
     */

    public function it_returns_default_for_requests_with_invalid_locale()
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $serverRequest->getQueryParams()
            ->willReturn(
                [
                    'lang' => 'hr',
                ]
            );

        $extractLocaleFromRequest = new ExtractLocaleFromRequest();
        $result = $extractLocaleFromRequest->__invoke($serverRequest->reveal());
        $this->assertEquals($result, Locale::DUTCH);
    }

    public function validLocalesProvider()
    {
        return [
            [Locale::FRENCH],
            [Locale::DUTCH],
        ];
    }
}
