<?php
namespace Ranyuen;

use Ranyuen\Di\Container;

/**
 * @example
 * <code>
 * (new \Ranyuen\App([]))->run();
 * </code>
 */
class App
{
    /** @var Container */
    private $_container;
    /** @var array */
    private $_config;
    /** @var Navigation */
    private $_nav;
    /** @var Router */
    private $_router;

    /**
     * @param array $config
     */
    public function __construct(array $config = null)
    {
        session_start();
        $env = isset($_ENV['SERVER_ENV']) ? $_ENV['SERVER_ENV'] : 'development';
        if ($env === 'development') {
            ini_set('display_errors', 1);
        }
        $this->_container = new Container();
        $this->loadServices($this->_container, $env);
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->_container;
    }

    /**
     * @return App
     */
    public function run()
    {
        $this->_container['db'];
        $this->_router->run();

        return $this;
    }

    /**
     * @param Container $container
     * @param string    $env
     */
    private function loadServices(Container $container, $env)
    {
        $container['config'] = function ($c) use ($env) {
            return (new Config())->load($env);
        };
        $container->bind('\Ranyuen\Logger', 'logger', function ($c) {
            $config = $c['config'];

            return new Logger($config['mode'], $config);
        });
        $container->bind('\Ranyuen\Navigation', 'nav', function ($c) {
            return new Navigation($c['config']);
        });
        $container->bind('\Ranyuen\Router', 'router', function ($c) {
            return new Router($this, $c['config']);
        });
        $container->bind('\Ranyuen\Db', 'db', function ($c) {
            return new DbCapsule($c['logger'], $c['config']['db']);
        });
        $this->_config = $container['config'];
        $this->_nav    = $container['nav'];
        $this->_router = $container['router'];
    }
}
