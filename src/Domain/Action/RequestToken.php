<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\InvalidDestination;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoDestinationPresent;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepository;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthService;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractDestinationUrlFromRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestToken
{
    /**
     * @var ExtractDestinationUrlFromRequest
     */
    private $extractDestinationUrlFromRequest;

    /**
     * @var DestinationUrlRepository
     */
    private $destinationUrlRepository;

    /**
     * @var AuthService
     */
    private $externalAuthService;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;


    public function __construct(
        ExtractDestinationUrlFromRequest $extractDestinationUrlFromRequest,
        DestinationUrlRepository $destinationUrlRepository,
        AuthService $externalAuthService,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->extractDestinationUrlFromRequest = $extractDestinationUrlFromRequest;
        $this->destinationUrlRepository = $destinationUrlRepository;
        $this->externalAuthService = $externalAuthService;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(ServerRequestInterface $serverRequest): ?ResponseInterface
    {
        try {
            $destinationUrl = $this->extractDestinationUrlFromRequest->__invoke($serverRequest);
            $this->destinationUrlRepository->storeDestinationUrl($destinationUrl);
            $this->externalAuthService->redirectToLogin();
            return null;
        } catch (NoDestinationPresent $exception) {
            return  $this->responseFactory->badRequestWithMessage($exception->getMessage());
        } catch (InvalidDestination $exception) {
            return  $this->responseFactory->badRequestWithMessage($exception->getMessage());
        }
    }
}
