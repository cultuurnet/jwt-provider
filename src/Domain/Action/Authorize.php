<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulAuthException;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\LoginServiceInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\GenerateAuthorizedDestinationUrl;
use Psr\Http\Message\ResponseInterface;

class Authorize
{
    /**
     * @var LoginServiceInterface
     */
    private $authService;

    /**
     * @var DestinationUrlRepositoryInterface
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
        LoginServiceInterface $authService,
        DestinationUrlRepositoryInterface $destinationUrlRepository,
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
        try {
            $token = $this->authService->token();
        } catch (UnSuccessfulAuthException $unSuccessfulAuth) {
            return $this->responseFactory->badRequest();
        }

        if ($token === null) {
            return $this->responseFactory->badRequest();
        }

        $destinationUrl = $this->destinationUrlRepository->getDestinationUrl();

        if ($destinationUrl === null) {
            return $this->responseFactory->badRequest();
        }

        $url = $this->generateAuthorizedDestinationUrl->__invoke(
            $destinationUrl,
            $token
        );

        return $this->responseFactory->redirectTo($url);
    }
}
