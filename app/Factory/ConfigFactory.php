<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Factory;

use Noodlehaus\Config;
use Noodlehaus\Parser\Yaml;

final class ConfigFactory
{
    public static function create(string $configDir): Config
    {
        $configFiles = [
            $configDir . '/config.yml',
        ];
        return Config::load($configFiles, new Yaml());
    }
}
