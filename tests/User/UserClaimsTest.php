<?php

namespace CultuurNet\UDB3\JwtProvider\User;

use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class UserClaimsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_be_converted_to_an_array()
    {
        $claims = new UserClaims(
            new StringLiteral('id-1'),
            new StringLiteral('foo'),
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
    public function it_can_have_an_empty_email_address_by_default()
    {
        $claims = new UserClaims(
            new StringLiteral('id-1'),
            new StringLiteral('foo')
        );

        $expected = [
            'uid' => 'id-1',
            'nick' => 'foo',
            'email' => '',
        ];

        $this->assertEquals($expected, $claims->toArray());
    }
}
