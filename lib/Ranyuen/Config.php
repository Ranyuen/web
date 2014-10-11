<?php
namespace Ranyuen;

use Liquid\Template;
use Symfony\Component\Yaml\Yaml;

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
            'templates.path' => 'view',
        ];
        $template = new Template();
        if (is_readable('config/common.yaml')) {
            $yaml = file_get_contents('config/common.yaml');
            $yaml = $template->parse($yaml)->render($_ENV);
            $config = array_merge($config, Yaml::parse($yaml));
        }
        if (is_readable("config/$env.yaml")) {
            $yaml = file_get_contents("config/$env.yaml");
            $yaml = $template->parse($yaml)->render($_ENV);
            $config = array_merge($config, Yaml::parse($yaml));
        }

        return $config;
    }
}
