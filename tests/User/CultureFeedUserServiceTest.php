<?php

namespace CultuurNet\UDB3\JwtProvider\User;

use CultuurNet\Auth\TokenCredentials;
use CultuurNet\Auth\User as AccessToken;
use CultuurNet\UDB3\JwtProvider\CultureFeed\CultureFeedFactoryInterface;
use ValueObjects\String\String as StringLiteral;
use ValueObjects\Web\EmailAddress;

class CultureFeedUserServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ICultureFeed|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cultureFeed;

    /**
     * @var CultureFeedFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cultureFeedFactory;

    /**
     * @var CultureFeedUserService
     */
    private $service;

    public function setUp()
    {
        $this->cultureFeed = $this->createMock(\ICultureFeed::class);

        $this->cultureFeedFactory = $this->createMock(CultureFeedFactoryInterface::class);

        $this->service = new CultureFeedUserService($this->cultureFeedFactory);
    }

    /**
     * @test
     * @dataProvider userServiceDataProvider
     *
     * @param AccessToken $accessToken
     * @param \CultureFeed_User $cfUser
     * @param UserClaims $expectedClaims
     * @internal param StringLiteral $id
     */
    public function it_returns_all_claims_for_a_user_by_user_id(
        AccessToken $accessToken,
        \CultureFeed_User $cfUser,
        UserClaims $expectedClaims
    ) {
        $includePrivateFields = true;
        $useAuth = true;

        $this->cultureFeed->expects($this->once())
            ->method('getUser')
            ->with(
                $accessToken->getId(),
                $includePrivateFields,
                $useAuth
            )
            ->willReturn($cfUser);

        $this->cultureFeedFactory->expects($this->once())
            ->method('createForUser')
            ->with($accessToken)
            ->willReturn($this->cultureFeed);

        $actualClaims = $this->service->getUserClaims($accessToken);

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
                new AccessToken(
                    'id-1',
                    new TokenCredentials('token1', 'secret1')
                ),
                $cfUserWithoutEmail,
                new UserClaims(
                    new StringLiteral('id-1'),
                    new StringLiteral('foo')
                ),
            ],
            [
                new AccessToken(
                    'id-2',
                    new TokenCredentials('token2', 'secret2')
                ),
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
