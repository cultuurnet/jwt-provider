<?php

namespace CultuurNet\UDB3\JwtProvider\User;

use PHPUnit\Framework\TestCase;

class UserClaimsTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $claims = new UserClaims(
            'id-1',
            'foo',
            new EmailAddress('foo@bar.com')
        );

        $expected = [
            'uid' => 'id-1',
            'nick' => 'foo',
            'email' => 'foo@bar.com',
        ];

        $this->assertEquals($expected, $claims->toArray());
    }

    /**
     * @test
     */
    public function it_can_have_an_empty_email_address_by_default(): void
    {
        $claims = new UserClaims(
            'id-1',
            'foo'
        );

        $expected = [
            'uid' => 'id-1',
            'nick' => 'foo',
            'email' => '',
        ];

        $this->assertEquals($expected, $claims->toArray());
    }
}
