<?php

namespace CultuurNet\UDB3\JwtProvider\RequestTokenStorage;

use Aura\Session\SessionFactory;
use CultuurNet\UDB3\JwtProvider\BaseServiceProvider;

class RequestTokenStorageServiceProvider extends BaseServiceProvider
{
    protected $provides = [
        RequestTokenStorageInterface::class,
    ];

    public function register(): void
    {
        $this->addShared(
            RequestTokenStorageInterface::class,
            function () {
                $sessionFactory = new SessionFactory;
                $session = $sessionFactory->newInstance($_COOKIE);
                $segment = $session->getSegment(RequestTokenSessionStorage::class);
                return new RequestTokenSessionStorage($segment);
            }
        );
    }
}
