<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ClientInformationRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LoginServiceInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\GenerateAuthorizedDestinationUrl;
use Psr\Http\Message\ResponseInterface;

final class Authorize
{
    private LoginServiceInterface $authService;
    private GenerateAuthorizedDestinationUrl $generateAuthorizedDestinationUrl;
    private ResponseFactoryInterface $responseFactory;
    private ClientInformationRepositoryInterface $clientInformationRepository;

    public function __construct(
        LoginServiceInterface $authService,
        GenerateAuthorizedDestinationUrl $generateAuthorizedDestinationUrl,
        ResponseFactoryInterface $responseFactory,
        ClientInformationRepositoryInterface $clientInformationRepository
    ) {
        $this->authService = $authService;
        $this->generateAuthorizedDestinationUrl = $generateAuthorizedDestinationUrl;
        $this->responseFactory = $responseFactory;
        $this->clientInformationRepository = $clientInformationRepository;
    }

    /**
     * @throws UnSuccessfulAuthException
     */
    public function __invoke(): ResponseInterface
    {
        $clientInformation = $this->clientInformationRepository->get();

        $url = $this->generateAuthorizedDestinationUrl->__invoke(
            $clientInformation->uri(),
            $this->authService->token(),
            $clientInformation->isAllowedRefresh() ? $this->authService->refreshToken() : ''
        );

        return $this->responseFactory->redirectTo($url);
    }
}
