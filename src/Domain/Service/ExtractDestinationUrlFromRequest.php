<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultuurNet\UDB3\JwtProvider\Domain\DestinationUrl;

use Psr\Http\Message\ServerRequestInterface;

class ExtractDestinationUrlFromRequest
{
    const DESTINATION = 'destination';

    public function __invoke(ServerRequestInterface $serverRequest): DestinationUrl
    {
        $queryParams = $serverRequest->getQueryParams();

        $this->guardAgainstNoDestinationPresent($queryParams);

        return DestinationUrl::fromString($queryParams[self::DESTINATION]);
    }

    private function guardAgainstNoDestinationPresent(array $queryParams): void
    {
        if (!isset($queryParams[self::DESTINATION])) {
            throw new \InvalidArgumentException(
                'Request does not contain a destination parameter to redirect to after login.'
            );
        }
    }
}
