<?php

namespace CultuurNet\UDB3\JwtProvider\CultureFeed;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\UDB3\JwtProvider\BaseServiceProvider;
use CultuurNet\UDB3\JwtProvider\User\CultureFeedUserService;
use CultuurNet\UDB3\JwtProvider\User\UserServiceInterface;
use ValueObjects\StringLiteral\StringLiteral;

class CultureFeedServiceProvider extends BaseServiceProvider
{
    protected $provides = [
        ConsumerCredentials::class,
        CultureFeedFactoryInterface::class,
        UserServiceInterface::class,
    ];

    public function register(): void
    {
        $this->addShared(
            CultureFeedFactoryInterface::class,
            function () {
                return new CultureFeedFactory(
                    $this->get(ConsumerCredentials::class),
                    new StringLiteral($this->parameter('uitid.base_url'))
                );
            }
        );

        $this->addShared(
            ConsumerCredentials::class,
            function () {
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
            function () {
                return $this->get(CultureFeedUserService::class);
            }
        );
    }
}
