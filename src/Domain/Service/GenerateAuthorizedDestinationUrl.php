<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use Psr\Http\Message\UriInterface;

class GenerateAuthorizedDestinationUrl
{
    public function __invoke(UriInterface $destinationUrl, string $token): UriInterface
    {
        $query = $destinationUrl->getQuery();
        $queryPrefix = $query !== '' ? '&' : '?';

        return $destinationUrl->withQuery($query . $queryPrefix . 'jwt=' . $token);
    }
}
