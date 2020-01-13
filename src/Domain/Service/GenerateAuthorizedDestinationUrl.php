<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultuurNet\UDB3\JwtProvider\Domain\Url;

class GenerateAuthorizedDestinationUrl
{
    public function __invoke(Url $destinationUrl, string $token): Url
    {
        $prefix = $this->generateQueryParameterPrefix($destinationUrl);
        return $destinationUrl->withAppendix($prefix . 'jwt=' . $token);
    }

    /**
     * @param Url $destinationUrl
     * @return string
     */
    private function generateQueryParameterPrefix(Url $destinationUrl): string
    {
        if ($this->hasQueryString($destinationUrl)) {
            return $prefix = '?';
        }
        return '&';
    }

    /**
     * @param Url $destinationUrl
     * @return bool
     */
    private function hasQueryString(Url $destinationUrl): bool
    {
        return strpos($destinationUrl->asString(), '?') === false;
    }
}
