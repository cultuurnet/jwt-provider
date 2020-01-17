<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthServiceInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractDestinationUrlFromRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Logout
{

    /**
     * @var ExtractDestinationUrlFromRequest
     */
    private $extractDestinationUrlFromRequest;

    /**
     * @var AuthServiceInterface
     */
    private $authService;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    public function __construct(
        ExtractDestinationUrlFromRequest $extractDestinationUrlFromRequest,
        AuthServiceInterface $authService,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->extractDestinationUrlFromRequest = $extractDestinationUrlFromRequest;
        $this->authService = $authService;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $destinationUrl = $this->extractDestinationUrlFromRequest->__invoke($serverRequest);
        $this->authService->logout();
        return $this->responseFactory->redirectTo($destinationUrl);
    }
}
