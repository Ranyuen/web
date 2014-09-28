<?php
/**
 * Ranyuen web entry point.
 *
 * PHP versions from 5.4 to 5.6
 *
 * @author Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 */

file_put_contents('php.pid', getmypid());
$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

require 'vendor/autoload.php';

$app = new \Ranyuen\App();
$app->run();
