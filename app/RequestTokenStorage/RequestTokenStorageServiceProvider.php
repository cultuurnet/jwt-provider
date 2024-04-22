<?php

namespace CultuurNet\UDB3\JwtProvider\RequestTokenStorage;

use Aura\Session\SessionFactory;
use CultuurNet\UDB3\JwtProvider\BaseServiceProvider;

class RequestTokenStorageServiceProvider extends BaseServiceProvider
{
    protected array $provides = [
        RequestTokenStorageInterface::class,
    ];

    public function provides(string $id): bool
    {
        return in_array($id, $this->provides, true);
    }

    public function register(): void
    {
        $this->addShared(
            RequestTokenStorageInterface::class,
            function (): RequestTokenSessionStorage {
                $sessionFactory = new SessionFactory;
                $session = $sessionFactory->newInstance($_COOKIE);
                $segment = $session->getSegment(RequestTokenSessionStorage::class);
                return new RequestTokenSessionStorage($segment);
            }
        );
    }
}
