<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen;

use Ranyuen\Di\Container;
use Ranyuen\Little\Request;
use Ranyuen\Little\Router;
use Exception;

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
    /**
     * DI container.
     *
     * @var Container
     */
    public $container;

    /**
     * Config.
     *
     * @Inject
     */
    private $config;
    /**
     * HTTP request router.
     *
     * @var Router
     *
     * @Inject
     */
    private $router;
    /**
     * Logger.
     *
     * @var Logger
     *
     * @Inject
     */
    private $logger;

    /**
     * Constructor.
     *
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
            case 'staging':
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
     * Run application.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function run()
    {
        try {
            $this->container['db']; // Prepare DB connection.
            $req = Request::createFromGlobals();
            if (preg_match('#\A/(ja|en|e)\W?#', $req->getPathInfo(), $matches)) {
                $lang = $matches[1];
                if (isset($this->config['lang'][$lang])) {
                    $lang = $this->config['lang'][$lang];
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
        } catch (Exception $e) {
            $this->logger->addError($e);
        }
    }

    /**
     * Load services on the DI container.
     *
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
            'Ranyuen\Navigation\Navigation',
            'nav',
            function ($c) {
                return $c->newInstance('Ranyuen\Navigation\Navigation');
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
        $c->facade('DB', 'db');
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
