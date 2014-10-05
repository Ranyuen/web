<?php
namespace Ranyuen;

use \Symfony\Component\Yaml\Yaml;

class Config
{
    private static $_config;

    /**
     * @param  string $filepath
     * @param  string $env
     * @return array
     */
    public function load($filepath = '', $env = 'development')
    {
        if (!is_null(self::$_config)) {
            return self::$_config;
        }
        if (!$filepath || !is_readable($filepath)) {
            return [];
        }
        $config = Yaml::parse(file_get_contents($filepath));
        $config = $this->setDefaultConfig($config, $env);
        self::$_config = $config;

        return $config;
    }

    /**
     * @param  array  $config
     * @param  string $env
     * @return array
     */
    private function setDefaultConfig($config, $env)
    {
        $set_default = function (&$array, $key, $dafault) {
            if (!isset($array[$key])) {
                $array[$key] = $dafault;
            }
        };

        // Configuration for Ranyuen App.
        $set_default($config, 'lang', []);
        $set_default($config['lang'], 'default', 'en');
        $set_default($config, 'layout', 'layout');
        $set_default($config, 'log.path', 'logs');
        $set_default($config, 'redirect', []);

        // Configuration for Slim Framwork.
        $set_default($config, 'debug', true);
        $set_default($config, 'log.enabled', false);
        $set_default($config, 'log.level', 7); // INFO
        $set_default($config, 'mode', $env);
        $set_default($config, 'templates.path', 'templates');

        return $config;
    }
}
