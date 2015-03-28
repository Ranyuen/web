<?php
/**
 * Ranyuen web site.
 *
 * PHP versions from 5.4 to 5.6
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/licenses/gpl.html GPL-3.0+
 */
file_put_contents('php.pid', getmypid());
$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (PHP_SAPI === 'cli-server' && is_file($filename)) {
    return false;
}

require 'vendor/autoload.php';
if (is_file('config/env.php')) {
    require_once 'config/env.php';
}

function h($text)
{
    return htmlspecialchars(
        $text,
        ENT_QUOTES|ENT_DISALLOWED|ENT_HTML5,
        'UTF-8'
    );
}

(new \Ranyuen\App())->run();
