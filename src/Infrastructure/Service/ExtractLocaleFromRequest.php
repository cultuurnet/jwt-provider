<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use Psr\Http\Message\ServerRequestInterface;

class ExtractLocaleFromRequest
{
    public function __invoke(ServerRequestInterface $serverRequest): ?string
    {
        $lang = $this->extractParameter($serverRequest);

        if (!in_array($lang, ['fr', 'nl'])) {
            return null;
        }

        return $lang;
    }

    private function extractParameter(ServerRequestInterface $serverRequest): ?string
    {
        $queryParams = $serverRequest->getQueryParams();
        return isset($queryParams['lang']) ? $queryParams['lang'] : null;
    }
}
