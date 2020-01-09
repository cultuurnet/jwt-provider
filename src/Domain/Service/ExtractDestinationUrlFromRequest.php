<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultuurNet\UDB3\JwtProvider\Domain\Url;

use Psr\Http\Message\ServerRequestInterface;

class ExtractDestinationUrlFromRequest
{
    const DESTINATION = 'destination';

    public function __invoke(ServerRequestInterface $serverRequest): Url
    {
        $queryParams = $serverRequest->getQueryParams();

        $this->guardAgainstNoDestinationPresent($queryParams);

        return Url::fromString($queryParams[self::DESTINATION]);
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
