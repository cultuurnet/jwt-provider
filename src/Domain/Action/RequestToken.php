<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoDestinationPresent;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepository;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthService;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractDestinationUrlFromRequest;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

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


    public function __construct(
        ExtractDestinationUrlFromRequest $extractDestinationUrlFromRequest,
        DestinationUrlRepository $destinationUrlRepository,
        AuthService $externalAuthService
    ) {
        $this->extractDestinationUrlFromRequest = $extractDestinationUrlFromRequest;
        $this->destinationUrlRepository = $destinationUrlRepository;
        $this->externalAuthService = $externalAuthService;
    }

    public function __invoke(ServerRequestInterface $serverRequest): ?ResponseInterface
    {
        try {
            $destinationUrl = $this->extractDestinationUrlFromRequest->__invoke($serverRequest);
            $this->destinationUrlRepository->storeDestinationUrl($destinationUrl);
            $this->externalAuthService->redirectToLogin();
            return null;
        } catch (NoDestinationPresent $exception) {
            $response = new Response(StatusCodeInterface::STATUS_BAD_REQUEST);
            $response->getBody()->write($exception->getMessage());
            return $response;
        }
    }
}
