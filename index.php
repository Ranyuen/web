<?php
file_put_contents('php.pid', getmypid());
$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) { return false; }

require 'vendor/autoload.php';

$app = new \Ranyuen\App;
$app->run();
