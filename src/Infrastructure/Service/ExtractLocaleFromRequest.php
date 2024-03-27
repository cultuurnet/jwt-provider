<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use CultuurNet\UDB3\JwtProvider\Domain\Enum\Locale;
use Psr\Http\Message\ServerRequestInterface;

final class ExtractLocaleFromRequest
{
    public function __invoke(ServerRequestInterface $serverRequest): string
    {
        $lang = $this->extractParameter($serverRequest);

        if (!in_array($lang, [Locale::DUTCH, Locale::FRENCH, Locale::GERMAN])) {
            return Locale::DUTCH;
        }

        return $lang;
    }

    private function extractParameter(ServerRequestInterface $serverRequest): ?string
    {
        $queryParams = $serverRequest->getQueryParams();
        return $queryParams['lang'] ?? null;
    }
}
