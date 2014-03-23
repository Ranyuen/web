<?php

use \Phpmig\Adapter;

$container = new ArrayObject();


$env = isset($_ENV['SERVER_ENV']) ? $_ENV['SERVER_ENV'] : 'development';
$config = new \Ranyuen\Config;
$config->load("config/$env.yaml");
$c = $config->container();
$container['db'] = $c['db'];

// replace this with a better Phpmig\Adapter\AdapterInterface
$container['phpmig.adapter'] = new Adapter\File\Flat(__DIR__ . DIRECTORY_SEPARATOR . 'migrations/.migrations.log');

$container['phpmig.migrations_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';

// You can also provide an array of migration files
// $container['phpmig.migrations'] = array_merge(
//     glob('migrations_1/*.php'),
//     glob('migrations_2/*.php')
// );

return $container;
