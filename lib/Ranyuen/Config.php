<?php
namespace Ranyuen;

use \ORM;
use \Pimple;
use \Symfony\Component\Yaml\Yaml;

class Config
{
    private static $container;

    public function __construct()
    {
        if (!self::$container) {
            self::$container = new Pimple;
        }
    }

    /**
     * @return Pimple
     */
    public function container()
    {
        return self::$container;
    }

    /**
     * @param  string $filepath
     * @return array
     */
    public function load($filepath = '')
    {
        if (isset(self::$container['config'])) {
            return self::$container['config'];
        }
        if (!$filepath || !is_readable($filepath)) {
            return [];
        }
        $config = Yaml::parse(file_get_contents($filepath));
        $this->fillContainer($config);

        return $config;
    }

    private function fillContainer($config)
    {
        self::$container['config'] = $config;
        ORM::configure($config['db']);
        ORM::configure('logging', true);
        self::$container['db'] = ORM::get_db();
    }
}
