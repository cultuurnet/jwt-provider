<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoDestinationPresentException;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ClientInformationRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Infrastructure\Service\ExtractClientInformationFromRequest;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LoginServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestToken
{
    /**
     * @var ExtractClientInformationFromRequest
     */
    private $extractClientInformationFromRequest;

    /**
     * @var LoginServiceInterface
     */
    private $externalAuthService;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var ClientInformationRepositoryInterface
     */
    private $clientInformationRepository;

    public function __construct(
        ExtractClientInformationFromRequest $extractClientInformationFromRequest,
        LoginServiceInterface $externalAuthService,
        ResponseFactoryInterface $responseFactory,
        ClientInformationRepositoryInterface $clientInformationRepository
    ) {
        $this->extractClientInformationFromRequest = $extractClientInformationFromRequest;
        $this->externalAuthService = $externalAuthService;
        $this->responseFactory = $responseFactory;
        $this->clientInformationRepository = $clientInformationRepository;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @return ResponseInterface|null
     * @throws NoDestinationPresentException
     */
    public function __invoke(ServerRequestInterface $serverRequest): ?ResponseInterface
    {
        $clientInformation = $this->extractClientInformationFromRequest->__invoke($serverRequest);
        $this->clientInformationRepository->store($clientInformation);
        return $this->externalAuthService->redirectToLogin();
    }
}
