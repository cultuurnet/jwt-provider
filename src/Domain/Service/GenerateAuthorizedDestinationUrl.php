<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use Psr\Http\Message\UriInterface;

final class GenerateAuthorizedDestinationUrl
{
    public function __invoke(UriInterface $destinationUrl, string $token, string $refreshToken = null): UriInterface
    {
        $query = [];
        parse_str($destinationUrl->getQuery(), $query);
        $query['jwt'] = $token;

        if ($refreshToken) {
            $query['refresh'] = $refreshToken;
        }

        $query = http_build_query($query);

        return $destinationUrl->withQuery($query);
    }
}
