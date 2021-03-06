<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen;

use Symfony\Component\Yaml\Yaml;
use Twig_Loader_Filesystem;
use Twig_Environment;

/**
 * Config loader.
 */
class Config
{
    private $dir = 'config/env';
    /**
     * Twig template engine.
     *
     * @var Twig_Environment
     */
    private $twig;

    public function __construct()
    {
        $loader = new Twig_Loader_Filesystem($this->dir);
        $this->twig = new Twig_Environment($loader);
    }

    /**
     * Load the config of current stage.
     *
     * @param string $env Stage. development or production or...
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function load($env = 'development')
    {
        $config = [
            'lang'           => ['default' => 'en'],
            'layout'         => 'layout',
            'log.path'       => 'logs',
            'redirect'       => [],
            'debug'          => true,
            'log.enabled'    => false,
            'log.level'      => 7, // INFO
            'mode'           => $env,
            'templates.path' => 'view',
        ];
        $mergeConfig = function ($config, $file) {
            if (!is_readable("$this->dir/$file")) {
                return $config;
            }
            $yaml = $this->twig->render($file, $_ENV);

            return array_merge($config, Yaml::parse($yaml));
        };
        $config = $mergeConfig($config, 'common.yaml');
        $config = $mergeConfig($config, "$env.yaml");

        return $config;
    }
}
