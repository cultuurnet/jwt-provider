<?php

namespace CultuurNet\UDB3\JwtProvider\User;

use ICultureFeed;
use ValueObjects\String\String as StringLiteral;
use ValueObjects\Web\EmailAddress;

class CultureFeedUserServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ICultureFeed|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cultureFeed;

    /**
     * @var CultureFeedUserService
     */
    private $service;

    public function setUp()
    {
        $this->cultureFeed = $this->getMock(ICultureFeed::class);
        $this->service = new CultureFeedUserService($this->cultureFeed);
    }

    /**
     * @test
     * @dataProvider userServiceDataProvider
     *
     * @param StringLiteral $id
     * @param \CultureFeed_User $cfUser
     * @param UserClaims $expectedClaims
     */
    public function it_returns_all_claims_for_a_user_by_user_id(
        StringLiteral $id,
        \CultureFeed_User $cfUser,
        UserClaims $expectedClaims
    ) {
        $this->cultureFeed->expects($this->once())
            ->method('getUser')
            ->with($id->toNative())
            ->willReturn($cfUser);

        $actualClaims = $this->service->getUserClaims($id);

        $this->assertEquals($expectedClaims, $actualClaims);
    }

    /**
     * @return array
     */
    public function userServiceDataProvider()
    {
        $cfUserWithoutEmail = new \CultureFeed_User();
        $cfUserWithoutEmail->id = 'id-1';
        $cfUserWithoutEmail->nick = 'foo';

        $cfUserWithEmail = clone $cfUserWithoutEmail;
        $cfUserWithEmail->id = 'id-2';
        $cfUserWithEmail->mbox = 'foo@bar.com';

        return [
            [
                new StringLiteral('id-1'),
                $cfUserWithoutEmail,
                new UserClaims(
                    new StringLiteral('id-1'),
                    new StringLiteral('foo')
                ),
            ],
            [
                new StringLiteral('id-2'),
                $cfUserWithEmail,
                new UserClaims(
                    new StringLiteral('id-2'),
                    new StringLiteral('foo'),
                    new EmailAddress('foo@bar.com')
                ),
            ],
        ];
    }
}
