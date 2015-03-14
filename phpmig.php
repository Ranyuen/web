<?php
/**
 * phpmig startup.
 */
require 'vendor/autoload.php';

use Phpmig\Adapter;
use Ranyuen\App;

$c = (new App())->container;
$c['schema'] = function ($c) {
    return $c['db']->getSchemaBuilder();
};
$c['phpmig.adapter'] = new Adapter\File\Flat(__DIR__.'/config/migrations/.migrations.log');
$c['phpmig.migrations_path'] = __DIR__.'/config/migrations';

return $c;
