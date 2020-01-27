<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoDestinationPresentException;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ApiKeyRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LoginServiceInterface;
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
     * @var LoginServiceInterface
     */
    private $externalAuthService;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var ApiKeyReaderInterface
     */
    private $apiKeyReader;

    /**
     * @var ApiKeyRepositoryInterface
     */
    private $apiKeyRepository;

    public function __construct(
        ExtractDestinationUrlFromRequest $extractDestinationUrlFromRequest,
        DestinationUrlRepositoryInterface $destinationUrlRepository,
        LoginServiceInterface $externalAuthService,
        ResponseFactoryInterface $responseFactory,
        ApiKeyReaderInterface $apiKeyReader,
        ApiKeyRepositoryInterface $apiKeyRepository
    ) {
        $this->extractDestinationUrlFromRequest = $extractDestinationUrlFromRequest;
        $this->destinationUrlRepository = $destinationUrlRepository;
        $this->externalAuthService = $externalAuthService;
        $this->responseFactory = $responseFactory;
        $this->apiKeyReader = $apiKeyReader;
        $this->apiKeyRepository = $apiKeyRepository;
    }

    public function __invoke(ServerRequestInterface $serverRequest): ?ResponseInterface
    {
        try {
            $destinationUrl = $this->extractDestinationUrlFromRequest->__invoke($serverRequest);
            $this->destinationUrlRepository->storeDestinationUrl($destinationUrl);
            $this->apiKeyRepository->storeApiKey($this->apiKeyReader->read($serverRequest));
            return $this->externalAuthService->redirectToLogin();
        } catch (NoDestinationPresentException $exception) {
            return $this->responseFactory->badRequestWithMessage($exception->getMessage());
        } catch (\InvalidArgumentException $exception) {
            return $this->responseFactory->badRequestWithMessage($exception->getMessage());
        }
    }
}
