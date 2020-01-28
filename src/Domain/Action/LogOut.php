<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepositoryInterface;
use Psr\Http\Message\ResponseInterface;

class LogOut
{
    /**
     * @var DestinationUrlRepositoryInterface
     */
    private $destinationUrlRepository;
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    public function __construct(
        DestinationUrlRepositoryInterface $destinationUrlRepository,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->destinationUrlRepository = $destinationUrlRepository;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(): ResponseInterface
    {
        $destinationUrl = $this->destinationUrlRepository->getDestinationUrl();

        if ($destinationUrl === null) {
            return $this->responseFactory->badRequest();
        }

        return $this->responseFactory->redirectTo($destinationUrl);
    }
}
