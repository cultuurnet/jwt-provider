<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider;

use CultuurNet\UDB3\JwtProvider\Console\DecodeJwtCommand;
use CultuurNet\UDB3\JwtProvider\Console\EncodeJwtCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

class CommandServiceProvider extends BaseServiceProvider
{
    protected array $provides = [
        Application::class,
    ];

    public function provides(string $id): bool
    {
        return in_array($id, $this->provides, true);
    }

    public function register(): void
    {
        $this->add(
            Application::class,
            function (): Application {
                $commandMap = [
                    'jwt:decode' => DecodeJwtCommand::class,
                    'jwt:encode' => EncodeJwtCommand::class,
                ];

                $application = new Application('udb3-search');

                $application->setCommandLoader(
                    new ContainerCommandLoader(
                        $this->getContainer(),
                        $commandMap
                    )
                );

                return $application;
            }
        );
    }
}
