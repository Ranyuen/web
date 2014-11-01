<?php
/**
 * Config loader.
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
    /** @var string */
    private $dir = 'config/env';
    /** @var Twig_Environment */
    private $twig;

    public function __construct()
    {
        $loader = new Twig_Loader_Filesystem($this->dir);
        $this->twig = new Twig_Environment($loader);
    }

    /**
     * @param string $env Stag. development or production or...
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function load($env = 'development')
    {
        $config = [
            // Configuration for Ranyuen App.
            'lang'           => ['default' => 'en'],
            'layout'         => 'layout',
            'log.path'       => 'logs',
            'redirect'       => [],
            // Configuration for Slim Framwork.
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
