<?php

namespace CultuurNet\UDB3\JwtProvider\Jwt;

use CultuurNet\Auth\TokenCredentials;
use CultuurNet\Auth\User as AccessToken;
use CultuurNet\UDB3\Jwt\JwtEncoderServiceInterface;
use CultuurNet\UDB3\JwtProvider\Http\RedirectResponse;
use CultuurNet\UDB3\JwtProvider\User\UserClaims;
use CultuurNet\UDB3\JwtProvider\User\UserServiceInterface;
use Lcobucci\JWT\Signature;
use Lcobucci\JWT\Token as Jwt;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\UriFactory;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class JwtOAuthCallbackHandlerTest extends TestCase
{
    /**
     * @var JwtEncoderServiceInterface|MockObject
     */
    private $encoder;

    /**
     * @var UserServiceInterface|MockObject
     */
    private $userService;

    private JwtOAuthCallbackHandler $callbackHandler;

    public function setUp(): void
    {
        $this->encoder = $this->createMock(JwtEncoderServiceInterface::class);
        $this->userService = $this->createMock(UserServiceInterface::class);

        $this->callbackHandler = new JwtOAuthCallbackHandler(
            $this->encoder,
            $this->userService
        );
    }

    /**
     * @test
     */
    public function it_returns_a_redirect_response_to_the_destination_with_a_jwt_as_url_fragment(): void
    {
        $userId = new StringLiteral('id-1');

        $accessToken = new AccessToken(
            $userId->toNative(),
            new TokenCredentials(
                'token',
                'secret'
            )
        );

        $destination =  (new UriFactory())->createUri('http://bar.com/sub/directory?query=value');

        $userClaims = new UserClaims(
            $userId,
            new StringLiteral('foo'),
            new EmailAddress('foo@bar.com')
        );

        $jwt = new Jwt(
            [
                'alg' => 'mocked',
            ],
            $userClaims->toArray(),
            new Signature('gibberish'),
            [
                'headers',
                'body',
                'gibberish',
            ]
        );

        $expectedDestination = 'http://bar.com/sub/directory?query=value&jwt=headers.body.gibberish';

        $this->userService->expects($this->once())
            ->method('getUserClaims')
            ->with($accessToken)
            ->willReturn($userClaims);

        $this->encoder->expects($this->once())
            ->method('encode')
            ->with($userClaims->toArray())
            ->willReturn($jwt);

        $response = $this->callbackHandler->handle($accessToken, $destination);
        $location = $response->getHeader('Location');

        /* @var RedirectResponse $response */
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($expectedDestination, reset($location));
    }
}
