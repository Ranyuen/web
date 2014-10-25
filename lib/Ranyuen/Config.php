<?php
namespace Ranyuen;

use Liquid\Template;
use Symfony\Component\Yaml\Yaml;

defined('LIQUID_INCLUDE_ALLOW_EXT') || define('LIQUID_INCLUDE_ALLOW_EXT', true);

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
        $merge_config = function ($config, $file) {
            $dir = 'config';
            if (!is_readable("$dir/$file")) {
                return $config;
            }
            $yaml = file_get_contents("$dir/$file");
            $yaml = (new Template($dir))->parse($yaml)->render($_ENV);

            return array_merge($config, Yaml::parse($yaml));
        };
        $config = $merge_config($config, 'env/common.yaml');
        $config = $merge_config($config, "env/$env.yaml");

        return $config;
    }
}
