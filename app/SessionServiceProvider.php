<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use Aura\Session\Session;
use Aura\Session\SessionFactory;

final class SessionServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        Session::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->addShared(
            Session::class,
            function () {
                $sessionFactory = new SessionFactory();
                return $sessionFactory->newInstance($_COOKIE);
            }
        );
    }
}
