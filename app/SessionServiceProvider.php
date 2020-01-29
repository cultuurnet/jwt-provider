<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use Aura\Session\Session;
use Aura\Session\SessionFactory;

class SessionServiceProvider extends BaseServiceProvider
{
    protected $provides = [
        Session::class
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->add(
            Session::class,
            function () {
                $sessionFactory = new SessionFactory;
                return $sessionFactory->newInstance($_COOKIE);
            }
        );
    }
}
