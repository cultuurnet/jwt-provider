#!/usr/bin/env php
<?php

use CultuurNet\UDB3\JwtProvider\Factory\ConfigFactory;
use CultuurNet\UDB3\JwtProvider\Factory\ContainerFactory;
use Symfony\Component\Console\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$config = ConfigFactory::create(__DIR__ . '/../');

$container = ContainerFactory::forCli($config);
$app = $container->get(Application::class);
$app->run();
