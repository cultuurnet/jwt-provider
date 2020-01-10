<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepository;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthService;
use CultuurNet\UDB3\JwtProvider\Domain\Service\GenerateAuthorizedDestinationUrl;
use Psr\Http\Message\ResponseInterface;

class Authorize
{
    /**
     * @var AuthService
     */
    private $authService;

    /**
     * @var DestinationUrlRepository
     */
    private $destinationUrlRepository;

    /**
     * @var GenerateAuthorizedDestinationUrl
     */
    private $generateAuthorizedDestinationUrl;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    public function __construct(
        AuthService $authService,
        DestinationUrlRepository $destinationUrlRepository,
        GenerateAuthorizedDestinationUrl $generateAuthorizedDestinationUrl,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->authService = $authService;
        $this->destinationUrlRepository = $destinationUrlRepository;
        $this->generateAuthorizedDestinationUrl = $generateAuthorizedDestinationUrl;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(): ResponseInterface
    {
        $token = $this->authService->token();

        if ($token === null) {
            return $this->responseFactory->badRequest();
        }

        $destinationUrl = $this->destinationUrlRepository->getDestinationUrl();

        if ($destinationUrl === null) {
            return $this->responseFactory->badRequest();
        }

        $url = $this->generateAuthorizedDestinationUrl->__invoke($destinationUrl,
            $token);

        return  $this->responseFactory->redirectTo($url);
    }
}
