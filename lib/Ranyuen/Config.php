<?php
namespace Ranyuen;

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
        if (is_readable('config/common.yaml')) {
            $config = array_merge($config,
                Yaml::parse(file_get_contents('config/common.yaml')));
        }
        if (is_readable("config/$env.yaml")) {
            $config = array_merge($config,
                Yaml::parse(file_get_contents("config/$env.yaml")));
        }

        return $config;
    }
}
