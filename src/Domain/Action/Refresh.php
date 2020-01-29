<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\BadRequestException;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulRefreshException;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\RefreshServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Refresh
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var RefreshServiceInterface
     */
    private $refreshService;

    public function __construct(ResponseFactoryInterface $responseFactory, RefreshServiceInterface $refreshService)
    {
        $this->responseFactory = $responseFactory;
        $this->refreshService = $refreshService;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @return ResponseInterface
     * @throws UnSuccessfulRefreshException
     * @throws BadRequestException
     */
    public function __invoke(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $body = $serverRequest->getParsedBody();

        if (!isset($body['refresh'])) {
            throw BadRequestException::missingRefreshToken();
        }

        $token = $this->refreshService->token($body['refresh']);

        return $this->responseFactory->responseWithToken($token);
    }
}
