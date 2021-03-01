<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Action;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\ClientInformationNotPresentException;
use CultuurNet\UDB3\JwtProvider\Domain\Factory\ResponseFactoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ClientInformationRepositoryInterface;
use Psr\Http\Message\ResponseInterface;

final class LogOut
{
    /**
     * @var ClientInformationRepositoryInterface
     */
    private $clientInformationRepository;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    public function __construct(
        ClientInformationRepositoryInterface $clientInformationRepository,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->clientInformationRepository = $clientInformationRepository;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(): ResponseInterface
    {
        $clientInformation = $this->clientInformationRepository->get();

        if ($clientInformation === null) {
            throw new ClientInformationNotPresentException();
        }

        return $this->responseFactory->redirectTo($clientInformation->uri());
    }
}
