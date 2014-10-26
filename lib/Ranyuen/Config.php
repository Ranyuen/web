<?php
namespace Ranyuen;

use Symfony\Component\Yaml\Yaml;
use Twig_Loader_Filesystem;
use Twig_Environment;

class Config
{
    /** @var string */
    private $_dir = 'config/env';
    /** @var Twig_Environment */
    private $_twig;

    public function __construct()
    {
        $loader = new Twig_Loader_Filesystem($this->_dir);
        $this->_twig = new Twig_Environment($loader);
    }

    /**
     * @param  string $env
     * @return array
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
        $merge_config = function ($config, $file) {
            if (!is_readable("$this->_dir/$file")) {
                return $config;
            }
            $yaml = $this->_twig->render($file, $_ENV);

            return array_merge($config, Yaml::parse($yaml));
        };
        $config = $merge_config($config, 'common.yaml');
        $config = $merge_config($config, "$env.yaml");

        return $config;
    }
}
