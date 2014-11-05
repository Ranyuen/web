<?php
/**
 * Ranyuen web site
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
    /**
     * @var array
     * @Inject
     */
    private $config;
    /**
     * @var Router
     * @Inject
     */
    private $router;

    /**
     * @param array $config Additional config. (Most config is written in config/env)
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __construct(array $config = null)
    {
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
        $this->container['db']; // Prepare DB connection.
        $this->router->run();

        return $this;
    }

    /**
     * @param Container $c   DI container
     * @param string    $env development or production or...
     *
     * @return void
     */
    private function loadServices(Container $c, $env)
    {
        $c['config'] = function ($c) use ($env) {
            return array_merge($this->config, (new Config())->load($env));
        };
        $c->bind(
            '\Ranyuen\Logger',
            'logger',
            function ($c) {
                $config = $c['config'];

                return new Logger($config['mode'], $config);
            }
        );
        $c->bind(
            '\Ranyuen\Navigation',
            'nav',
            function ($c) {
                return new Navigation($c['config']);
            }
        );
        $c->bind(
            '\Ranyuen\Router',
            'router',
            function ($c) {
                return new Router($this, $c['config']);
            }
        );
        $c->bind(
            '\Ranyuen\Db',
            'db',
            function ($c) {
                return new DbCapsule($c['logger'], $c['config']['db']);
            }
        );
        $c->bind(
            '\Ranyuen\Renderer',
            'renderer',
            function ($c) {
                $config = $c['config'];

                return (new Renderer($config['templates.path']))
                    ->setLayout($config['layout'])
                    ->addHelper(new Helper\Helper($config));
            }
        );
        $c->bind(
            '\Ranyuen\Session',
            'session',
            function ($c) {
                return new Session();
            }
        );
        $c->bind(
            '\Ranyuen\BgImage',
            'bgimage',
            function ($c) {
                return $c->newInstance('\Ranyuen\BgImage');
            }
        );
        $c->inject($this);
    }
}
