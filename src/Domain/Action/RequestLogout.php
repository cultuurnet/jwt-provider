<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LoginServiceInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractDestinationUrlFromRequest;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LogOutServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestLogout
{

    /**
     * @var ExtractDestinationUrlFromRequest
     */
    private $extractDestinationUrlFromRequest;

    /**
     * @var LoginServiceInterface
     */
    private $logOutService;

    /**
     * @var DestinationUrlRepositoryInterface
     */
    private $destinationUrlRepository;

    public function __construct(
        ExtractDestinationUrlFromRequest $extractDestinationUrlFromRequest,
        LogOutServiceInterface $authService,
        DestinationUrlRepositoryInterface $destinationUrlRepository
    ) {
        $this->extractDestinationUrlFromRequest = $extractDestinationUrlFromRequest;
        $this->logOutService = $authService;
        $this->destinationUrlRepository = $destinationUrlRepository;
    }

    public function __invoke(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $destinationUrl = $this->extractDestinationUrlFromRequest->__invoke($serverRequest);
        $this->destinationUrlRepository->storeDestinationUrl($destinationUrl);

        return $this->logOutService->logout();
    }
}
