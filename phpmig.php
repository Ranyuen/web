<?php
require 'vendor/autoload.php';

use \Phpmig\Adapter;
use \Ranyuen\App;

$container = new ArrayObject();
$c = (new App())->getContainer();
$container['db'] = $c['db'];
$container['phpmig.adapter'] = new Adapter\File\Flat(__DIR__ . DIRECTORY_SEPARATOR . 'migrations/.migrations.log');
$container['phpmig.migrations_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';

return $container;
