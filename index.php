<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use WPD\Console\Command\WPCLICommand;
use WPD\Console\Command\GeneratorCommand;

$app = new Application();

$app->add(new WPCLICommand());
$app->add(new GeneratorCommand());

$app->run();
