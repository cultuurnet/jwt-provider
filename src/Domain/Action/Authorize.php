<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepository;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthService;
use CultuurNet\UDB3\JwtProvider\Domain\Service\GenerateAuthorizedDestinationUrl;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

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

    public function __construct(
        AuthService $authService,
        DestinationUrlRepository $destinationUrlRepository,
        GenerateAuthorizedDestinationUrl $generateAuthorizedDestinationUrl
    ) {
        $this->authService = $authService;
        $this->destinationUrlRepository = $destinationUrlRepository;
        $this->generateAuthorizedDestinationUrl = $generateAuthorizedDestinationUrl;
    }

    public function __invoke(): ResponseInterface
    {
        $token = $this->authService->token();

        if ($token === null) {
            return new Response(StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $destinationUrl = $this->destinationUrlRepository->getDestinationUrl();

        if ($destinationUrl === null) {
            return new Response(StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $url = $this->generateAuthorizedDestinationUrl->__invoke($destinationUrl,
            $token);

        return new Response(
            StatusCodeInterface::STATUS_MOVED_PERMANENTLY,
            new Headers(
                ['Location' => $url->asString()]
            )
        );
    }
}
