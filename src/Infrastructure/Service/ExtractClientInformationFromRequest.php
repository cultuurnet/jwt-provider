<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoDestinationPresentException;
use CultuurNet\UDB3\JwtProvider\Domain\Service\ExtractClientInformationFromRequestInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Service\IsAllowedRefreshTokenInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Value\ClientInformation;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;

final class ExtractClientInformationFromRequest implements ExtractClientInformationFromRequestInterface
{
    public const DESTINATION = 'destination';

    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;

    /**
     * @var ApiKeyReaderInterface
     */
    private $apiKeyReader;

    /**
     * @var IsAllowedRefreshToken
     */
    private $isAllowedRefreshToken;

    public function __construct(
        UriFactoryInterface $uriFactory,
        ApiKeyReaderInterface $apiKeyReader,
        IsAllowedRefreshTokenInterface $isAllowedRefreshToken
    ) {
        $this->uriFactory = $uriFactory;
        $this->apiKeyReader = $apiKeyReader;
        $this->isAllowedRefreshToken = $isAllowedRefreshToken;
    }

    /**
     * @throws NoDestinationPresentException
     */
    public function __invoke(ServerRequestInterface $serverRequest): ClientInformation
    {
        $destination = $this->extractDestination($serverRequest);

        $apiKey = $this->apiKeyReader->read($serverRequest);

        return new ClientInformation(
            $this->uriFactory->createUri($destination),
            $apiKey,
            $this->isAllowedRefreshToken->__invoke($apiKey)
        );
    }

    private function extractDestination(ServerRequestInterface $serverRequest): string
    {
        $queryParams = $serverRequest->getQueryParams();

        if (!isset($queryParams[self::DESTINATION])) {
            throw new NoDestinationPresentException();
        }

        return $queryParams[self::DESTINATION];
    }
}
