<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen;

use Ranyuen\Di\Container;
use Ranyuen\Little\Request;
use Ranyuen\Little\Router;

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
    public $container;

    /** @Inject */
    private $config;
    /**
     * @var Router
     * @Inject
     */
    private $router;
    /**
     * @var Logger
     * @Inject
     */
    private $logger;

    /**
     * @param array $config Additional config. (Most config is written in config/env)
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __construct(array $config = null)
    {
        $env = isset($_ENV['SERVER_ENV']) ? $_ENV['SERVER_ENV'] : 'development';
        switch ($env) {
            case 'production':
                ini_set('display_errors', 0);
                break;
            case 'development':
            default:
                ini_set('display_errors', 1);
        }
        error_reporting(E_ALL | E_STRICT);
        set_error_handler(
            function ($errno, $errstr, $errfile, $errline, $errcontext) {
                echo "Err$errno:$errstr\n$errfile:$errline\n";
                var_dump($errcontext);
            }
        );
        set_exception_handler(
            function ($ex) {
                echo "$ex\n";
            }
        );
        $this->container = new Container();
        $this->loadServices($this->container, $env, $config ? $config : []);
    }

    /**
     * @return void
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function run()
    {
        $this->container['db']; // Prepare DB connection.
        $req = Request::createFromGlobals();
        if (preg_match('#\A/(ja|en|e)#', $req->getPathInfo(), $matches)) {
            $lang = $matches[1];
            if (isset($config['lang'][$lang])) {
                $lang = $config['lang'][$lang];
            }
            $req->query->set('lang', $lang);
            $server = $_SERVER;
            $server['REQUEST_URI'] = substr($req->getPathInfo(), strlen($lang) + 1);
            $req = $req->duplicate(null, null, null, null, null, $server);
        }
        if (!$req->get('lang')) {
            $req->query->set('lang', $this->config['lang']['default']);
        }
        $this->router->run($req)->send();
        $this->logger->addAccessInfo();
    }

    /**
     * @param Container $c      DI container
     * @param string    $env    development or production or...
     * @param array     $config Additional config.
     *
     * @return void
     */
    private function loadServices(Container $c, $env, array $config)
    {
        $c->bind('Ranyuen\App', 'app', $this);
        $c['config'] = function ($c) use ($env, $config) {
            return array_merge((new Config())->load($env), $config);
        };
        $c->bind(
            'Ranyuen\Logger',
            'logger',
            function ($c) {
                return $c->newInstance('Ranyuen\Logger', [$c['config']['mode']]);
            }
        );
        $c->bind(
            'Ranyuen\Navigation',
            'nav',
            function ($c) {
                return $c->newInstance('Ranyuen\Navigation');
            }
        );
        $c->bind(
            'Ranyuen\Little\Router',
            'router',
            function ($container) {
                $router = new Router($container);
                include_once 'config/routes.php';

                return $router;
            }
        );
        $c->bind(
            'Ranyuen\DbCapsule',
            'db',
            function ($c) {
                return $c->newInstance('Ranyuen\DbCapsule');
            }
        );
        $c->bind(
            'Ranyuen\Template\ViewRenderer',
            'renderer',
            $c->factory(
                function ($c) {
                    $config = $c['config'];
                    $renderer = (new Template\ViewRenderer($config['templates.path']));
                    $renderer->setLayout($config['layout']);
                    $renderer->addHelper($c->newInstance('Ranyuen\Helper\MainHelper'));

                    return $renderer;
                }
            )
        );
        $c->bind(
            'Ranyuen\Session',
            'session',
            function ($c) {
                return new Session();
            }
        );
        $c->bind(
            'Ranyuen\BgImage',
            'bgimage',
            function ($c) {
                return $c->newInstance('Ranyuen\BgImage');
            }
        );
        $c->inject($this);
    }
}
