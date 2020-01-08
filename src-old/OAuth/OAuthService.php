<?php

namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\Auth\Guzzle\Service as BaseService;
use Guzzle\Http\QueryString;
use Guzzle\Http\Url;

class OAuthService extends BaseService
{
    public function getLogoutUrl(string $destination): Url
    {
        $url = $this->getUrlForPath('/auth/logout');
        $url->setQuery(
            new QueryString(['destination' => $destination])
        );
        return $url;
    }
}
