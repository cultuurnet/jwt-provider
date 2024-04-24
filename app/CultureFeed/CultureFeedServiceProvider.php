<?php

namespace CultuurNet\UDB3\JwtProvider\CultureFeed;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\UDB3\JwtProvider\BaseServiceProvider;
use CultuurNet\UDB3\JwtProvider\User\CultureFeedUserService;
use CultuurNet\UDB3\JwtProvider\User\UserServiceInterface;

class CultureFeedServiceProvider extends BaseServiceProvider
{
    protected array $provides = [
        ConsumerCredentials::class,
        CultureFeedFactoryInterface::class,
        UserServiceInterface::class,
    ];

    public function provides(string $id): bool
    {
        return in_array($id, $this->provides, true);
    }

    public function register(): void
    {
        $this->addShared(
            CultureFeedFactoryInterface::class,
            fn(): CultureFeedFactory => new CultureFeedFactory(
                $this->get(ConsumerCredentials::class),
                $this->parameter('uitid.base_url')
            )
        );

        $this->addShared(
            ConsumerCredentials::class,
            function (): ConsumerCredentials {
                $key = $this->parameter('uitid.consumer.key');
                $secret = $this->parameter('uitid.consumer.secret');

                return new ConsumerCredentials(
                    $key,
                    $secret
                );
            }
        );

        $this->addShared(
            UserServiceInterface::class,
            fn() => $this->get(CultureFeedUserService::class)
        );
    }
}
