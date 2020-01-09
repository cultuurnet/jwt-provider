<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoTokenPresent;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepository;
use CultuurNet\UDB3\JwtProvider\Domain\Service\AuthService;
use CultuurNet\UDB3\JwtProvider\Domain\Service\GenerateAuthorizedDestinationUrl;
use CultuurNet\UDB3\JwtProvider\Domain\Url;

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

    public function __invoke(): Url
    {
        $token = $this->authService->token();

        if ($token === null) {
            throw new NoTokenPresent();
        }
        return $this->generateAuthorizedDestinationUrl->__invoke($this->destinationUrlRepository->getDestinationUrl(), $token);
    }
}
