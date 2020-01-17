<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\InvalidDestinationException;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoDestinationPresentException;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthServiceInterface;
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
     * @var DestinationUrlRepositoryInterface
     */
    private $destinationUrlRepository;

    /**
     * @var AuthServiceInterface
     */
    private $externalAuthService;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;


    public function __construct(
        ExtractDestinationUrlFromRequest $extractDestinationUrlFromRequest,
        DestinationUrlRepositoryInterface $destinationUrlRepository,
        AuthServiceInterface $externalAuthService,
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
            return $this->externalAuthService->redirectToLogin();
        } catch (NoDestinationPresentException $exception) {
            return $this->responseFactory->badRequestWithMessage($exception->getMessage());
        } catch (\InvalidArgumentException $exception) {
            return $this->responseFactory->badRequestWithMessage($exception->getMessage());
        }
    }
}
