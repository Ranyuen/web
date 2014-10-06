<?php
namespace Ranyuen;

use \Mustache_Engine;
use \Symfony\Component\Yaml\Yaml;

class Config
{
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
            'templates.path' => 'templates',
        ];
        $mustache = new Mustache_Engine();
        if (is_readable('config/common.yaml')) {
            $yaml = file_get_contents('config/common.yaml');
            $yaml = $mustache->render($yaml, $_ENV);
            $config = array_merge($config, Yaml::parse($yaml));
        }
        if (is_readable("config/$env.yaml")) {
            $yaml = file_get_contents("config/$env.yaml");
            $yaml = $mustache->render($yaml, $_ENV);
            $config = array_merge($config, Yaml::parse($yaml));
        }

        return $config;
    }
}
