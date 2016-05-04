#!/usr/bin/env php
<?php

use CultuurNet\UDB3\JwtProvider\Console\DecodeJwtCommand;
use CultuurNet\UDB3\JwtProvider\Console\EncodeJwtCommand;
use Knp\Provider\ConsoleServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';

/** @var \Silex\Application $app */
$app = require __DIR__ . '/../bootstrap.php';

$app->register(
    new ConsoleServiceProvider(),
    [
        'console.name'              => 'MyApplication',
        'console.version'           => '1.0.0',
        'console.project_directory' => __DIR__.'/..'
    ]
);

/** @var \Knp\Console\Application $consoleApp */
$consoleApp = $app['console'];

$consoleApp->add(new EncodeJwtCommand('jwt.encoder'));
$consoleApp->add(new DecodeJwtCommand('jwt.decoder'));

$consoleApp->run();
