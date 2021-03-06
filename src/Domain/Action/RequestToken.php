<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoDestinationPresentException;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ClientInformationRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractClientInformationFromRequestInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LoginServiceInterface;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\ExtractLocaleFromRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RequestToken
{
    /**
     * @var ExtractClientInformationFromRequestInterface
     */
    private $extractClientInformationFromRequest;

    /**
     * @var LoginServiceInterface
     */
    private $externalAuthService;

    /**
     * @var ClientInformationRepositoryInterface
     */
    private $clientInformationRepository;

    /**
     * @var ExtractLocaleFromRequest
     */
    private $extractLocaleFromRequest;

    public function __construct(
        ExtractClientInformationFromRequestInterface $extractClientInformationFromRequest,
        LoginServiceInterface $externalAuthService,
        ClientInformationRepositoryInterface $clientInformationRepository,
        ExtractLocaleFromRequest $extractLocaleFromRequest
    ) {
        $this->extractClientInformationFromRequest = $extractClientInformationFromRequest;
        $this->externalAuthService = $externalAuthService;
        $this->clientInformationRepository = $clientInformationRepository;
        $this->extractLocaleFromRequest = $extractLocaleFromRequest;
    }

    /**
     * @throws NoDestinationPresentException
     */
    public function __invoke(ServerRequestInterface $serverRequest): ?ResponseInterface
    {
        $clientInformation = $this->extractClientInformationFromRequest->__invoke($serverRequest);
        $this->clientInformationRepository->store($clientInformation);
        return $this->externalAuthService->redirectToLogin($this->extractLocaleFromRequest->__invoke($serverRequest));
    }
}
