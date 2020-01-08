<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepository;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExternalAuthService;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractDestinationUrlFromRequest;
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
     * @var ExternalAuthService
     */
    private $externalAuthService;


    public function __construct(
        ExtractDestinationUrlFromRequest $extractDestinationUrlFromRequest,
        DestinationUrlRepository $destinationUrlRepository,
        ExternalAuthService $externalAuthService
    ) {
        $this->extractDestinationUrlFromRequest = $extractDestinationUrlFromRequest;
        $this->destinationUrlRepository = $destinationUrlRepository;
        $this->externalAuthService = $externalAuthService;
    }

    public function __invoke(ServerRequestInterface $serverRequest): void
    {
        $destinationUrl = $this->extractDestinationUrlFromRequest->__invoke($serverRequest);

        $this->destinationUrlRepository->storeDestinationUrl($destinationUrl);

        $this->externalAuthService->redirectToLogin();
    }
}
