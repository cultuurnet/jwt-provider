<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\NoDestinationPresentException;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

class ExtractDestinationUrlFromRequest
{
    const DESTINATION = 'destination';
    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;

    public function __construct(UriFactoryInterface $uriFactory)
    {
        $this->uriFactory = $uriFactory;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @return UriInterface
     * @throws NoDestinationPresentException
     * @throws InvalidArgumentException
     */
    public function __invoke(ServerRequestInterface $serverRequest): UriInterface
    {
        $queryParams = $serverRequest->getQueryParams();

        $this->guardAgainstNoDestinationPresent($queryParams);
        return $this->uriFactory->createUri($queryParams[self::DESTINATION]);
    }

    /**
     * @param array $queryParams
     * @throws NoDestinationPresentException
     */
    private function guardAgainstNoDestinationPresent(array $queryParams): void
    {
        if (!isset($queryParams[self::DESTINATION])) {
            throw new NoDestinationPresentException();
        }
    }
}
