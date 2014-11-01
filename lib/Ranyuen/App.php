<?php
/**
 * Application main class.
 */
namespace Ranyuen;

use Ranyuen\Di\Container;

/**
 * Application main class.
 *
 * @example
 * <code>
 * (new \Ranyuen\App([]))->run();
 * </code>
 */
class App
{
    /** @var Container */
    private $container;
    /** @var array */
    private $config;
    /** @var Navigation */
    private $nav;
    /** @var Router */
    private $router;

    /**
     * @param array $config Additional config. (Most config is written in config/env)
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __construct(array $config = null)
    {
        session_start();
        $env = isset($_ENV['SERVER_ENV']) ? $_ENV['SERVER_ENV'] : 'development';
        if ('development' === $env) {
            ini_set('display_errors', 1);
            error_reporting(E_ALL | E_STRICT);
        }
        // set_error_handler(function () {});
        // set_exception_handler(function () {});
        $this->config = $config ? $config : [];
        $this->container = new Container();
        $this->loadServices($this->container, $env);
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return App
     */
    public function run()
    {
        $this->container['db'];
        $this->router->run();

        return $this;
    }

    /**
     * @param Container $container DI container
     * @param string    $env       development or production or...
     *
     * @return void
     */
    private function loadServices(Container $container, $env)
    {
        $container['config'] = function ($c) use ($env) {
            return array_merge($this->config, (new Config())->load($env));
        };
        $container->bind(
            '\Ranyuen\Logger',
            'logger',
            function ($c) {
                $config = $c['config'];

                return new Logger($config['mode'], $config);
            }
        );
        $container->bind(
            '\Ranyuen\Navigation',
            'nav',
            function ($c) {
                return new Navigation($c['config']);
            }
        );
        $container->bind(
            '\Ranyuen\Router',
            'router',
            function ($c) {
                return new Router($this, $c['config']);
            }
        );
        $container->bind(
            '\Ranyuen\Db',
            'db',
            function ($c) {
                return new DbCapsule($c['logger'], $c['config']['db']);
            }
        );
        $this->config = $container['config'];
        $this->nav    = $container['nav'];
        $this->router = $container['router'];
    }
}
