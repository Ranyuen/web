<?php
namespace Ranyuen;

use \ORM;
use \Pimple;
use \Symfony\Component\Yaml\Yaml;

class Config
{
    private static $_container;

    public function __construct()
    {
        if (!self::$_container) {
            self::$_container = new Pimple();
        }
    }

    /**
     * @return Pimple
     */
    public function container()
    {
        return self::$_container;
    }

    /**
     * @param  string $filepath
     * @return array
     */
    public function load($filepath = '')
    {
        if (isset(self::$_container['config'])) {
            return self::$_container['config'];
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
        self::$_container['config'] = $config;
        ORM::configure($config['db']);
        ORM::configure('logging', true);
        self::$_container['db'] = ORM::get_db();
    }
}
