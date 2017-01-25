<?php

namespace CultuurNet\UDB3\JwtProvider\OAuth;

use CultuurNet\Auth\Guzzle\Service as BaseService;
use Guzzle\Http\QueryString;
use Guzzle\Http\Url;

class OAuthService extends BaseService
{
    /**
     * @param string $destination
     * @return Url
     */
    public function getLogoutUrl($destination)
    {
        $url = parent::getUrlForPath('/auth/logout');
        $url->setQuery(
            new QueryString(['destination' => $destination])
        );

        return $url;
    }
}
