<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use Psr\Http\Message\UriInterface;

class GenerateAuthorizedDestinationUrl
{
    public function __invoke(UriInterface $destinationUrl, string $token, string $refreshToken = null): UriInterface
    {
        $query = $destinationUrl->getQuery();
        $queryPrefix = $query !== '' ? '&' : '?';
        $refreshAppendix = $refreshToken !== null ? '&refresh=' . $refreshToken : '';
        return $destinationUrl->withQuery($query . $queryPrefix . 'jwt=' . $token . $refreshAppendix);
    }
}
