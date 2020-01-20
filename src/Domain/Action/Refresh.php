<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;
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

    public function __invoke(ServerRequestInterface $serverRequest): ResponseInterface
    {
        try {
            $this->refreshService->renewToken();
            $token = $this->refreshService->token();
            return $this->responseFactory->responseWithToken($token);
        } catch (UnSuccessfulAuthException $e) {
            return $this->responseFactory->badRequest();
        }
    }
}
