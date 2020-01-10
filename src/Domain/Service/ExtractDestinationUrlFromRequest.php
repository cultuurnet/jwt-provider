<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use Assert\AssertionFailedException;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\InvalidDestination;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoDestinationPresent;
use CultuurNet\UDB3\JwtProvider\Domain\Url;

use Psr\Http\Message\ServerRequestInterface;

class ExtractDestinationUrlFromRequest
{
    const DESTINATION = 'destination';

    /**
     * @param ServerRequestInterface $serverRequest
     * @return Url
     * @throws NoDestinationPresent
     * @throws InvalidDestination
     */
    public function __invoke(ServerRequestInterface $serverRequest): Url
    {
        $queryParams = $serverRequest->getQueryParams();

        $this->guardAgainstNoDestinationPresent($queryParams);

        return $this->createDestinationUrl($queryParams);
    }

    /**
     * @param array $queryParams
     * @throws NoDestinationPresent
     */
    private function guardAgainstNoDestinationPresent(array $queryParams): void
    {
        if (!isset($queryParams[self::DESTINATION])) {
            throw new NoDestinationPresent();
        }
    }

    /**
     * @param array $queryParams
     * @return Url
     * @throws InvalidDestination
     */
    private function createDestinationUrl(array $queryParams): Url
    {
        try {
            return Url::fromString($queryParams[self::DESTINATION]);
        } catch (AssertionFailedException $e) {
            throw new InvalidDestination($queryParams[self::DESTINATION]);
        }
    }
}
