<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoDestinationPresentException;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ClientInformationRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractClientInformationFromRequestInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LogOutServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RequestLogout
{
    private \CultuurNet\UDB3\JwtProvider\Domain\Service\LogOutServiceInterface $logOutService;

    private \CultuurNet\UDB3\JwtProvider\Domain\Repository\ClientInformationRepositoryInterface $clientInformationRepository;

    private \CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractClientInformationFromRequestInterface $extractClientInformationFromRequest;

    public function __construct(
        ExtractClientInformationFromRequestInterface $extractClientInformationFromRequest,
        LogOutServiceInterface $authService,
        ClientInformationRepositoryInterface $clientInformationRepository
    ) {
        $this->logOutService = $authService;
        $this->clientInformationRepository = $clientInformationRepository;
        $this->extractClientInformationFromRequest = $extractClientInformationFromRequest;
    }

    /**
     * @throws NoDestinationPresentException
     */
    public function __invoke(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $clientInformation = $this->extractClientInformationFromRequest->__invoke($serverRequest);
        $this->clientInformationRepository->store($clientInformation);

        return $this->logOutService->logout();
    }
}
