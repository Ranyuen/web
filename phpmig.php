<?php
/**
 * phpmig startup.
 */
require 'vendor/autoload.php';

use \Phpmig\Adapter;
use \Ranyuen\App;

$container = (new App())->getContainer();
$container['schema'] = function ($c) {
    return $c['db']->getSchemaBuilder();
};
$container['phpmig.adapter'] = new Adapter\File\Flat(__DIR__.DIRECTORY_SEPARATOR.'migrations/.migrations.log');
$container['phpmig.migrations_path'] = __DIR__.DIRECTORY_SEPARATOR.'migrations';

return $container;
