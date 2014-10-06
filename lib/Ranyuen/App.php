<?php
namespace Ranyuen;

use \Pimple\Container;

/**
 * Quick start
 * ===========
 * ```php
 * (new \Ranyuen\App([]))->run();
 * ```
 */
class App
{
    /** @type Container */
    private $_container;
    /** @type array */
    private $_config;
    /** @type Router */
    private $_router;
    /** @type DbCapsule */
    private $_db;

    /**
     * @param array $config
     */
    public function __construct($config = null)
    {
        session_start();
        $env = isset($_ENV['SERVER_ENV']) ? $_ENV['SERVER_ENV'] : 'development';
        if ($env === 'development') {
            ini_set('display_errors', 1);
        }
        $this->_container = new Container();
        $this->loadServices($this->_container, $env);
        $this->_config = $this->_container['config'];
        $this->_router = $this->_container['router'];
        $this->applyDefaultRoutes($this->_container['logger']);
        $this->_db = $this->_container['db'];
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
        $this->_router->run();

        return $this;
    }

    /**
     * @param  string $lang
     * @param  string $template_name
     * @param  array  $params
     * @return App
     */
    public function render($lang, $template_name, $params = [])
    {
        $renderer = new Renderer($this->_config);
        if (isset($this->_config['lang'][$lang])) {
            $lang = $this->_config['lang'][$lang];
        }
        $this->mergeParams($lang, $template_name, $params);
        $str = $renderer
            ->setLayout($this->_config['layout'])
            ->render("$template_name.$lang", $params);
        if ($str === false) {
            $this->_router->notFound();
        } else {
            echo $str;
        }

        return $this;
    }

    /**
     * @param  string   $api_name
     * @param  string   $method
     * @param  string[] $uri_params
     * @param  array    $request_params
     * @return App
     */
    public function renderApi($api_name, $method, $uri_params, $request_params)
    {
        $api_name = preg_replace_callback('/[-_](.)/', function ($m) {
            return strtoupper($m[1]);
        }, ucwords(strtolower($api_name)));
        $controller = (new \ReflectionClass("\Ranyuen\\Controller\\Api$api_name"))->newInstance();
        $response = $controller->render($method, $uri_params, $request_params);
        if (!$response) { return $this; }
        echo is_array($response) ? json_encode($response) : $response;

        return $this;
    }

    /**
     * @param Container $container
     */
    private function loadServices(Container $container, $env)
    {
        $container['config'] = function ($c) use ($env) {
            return (new Config())->load("config/$env.yaml", $env);
        };
        $container['router'] = function ($c) {
            return new Router($c['config']);
        };
        $container['logger'] = function ($c) {
            $config = $c['config'];

            return new Logger($config['mode'], $config);
        };
        $container['db'] = function ($c) {
            return new DbCapsule($c['config']['db'], $c['logger']);
        };
    }

    private function applyDefaultRoutes(Logger $logger)
    {
        $router = $this->_router;
        $controller = function ($lang, $path) use ($router, $logger) {
            foreach ($this->_config['redirect'] as $src => $dest) {
                if ($_SERVER['REQUEST_URI'] === $src) {
                    $router->redirect($dest, 301);
                }
            }
            $this->render($lang, $path);
            $logger->addAccessInfo();
        };
        $router->get('/api/:path+', function ($path) use ($router) {
            $this->renderApi($path[0], 'GET', array_slice($path, 1),
                $router->request->get());
        });
        $router->get('/api/:path+', function ($path) use ($router) {
            $this->renderApi($path[0], 'POST', array_slice($path, 1),
                $router->request->post());
        });
        $router->get('/api/:path+', function ($path) use ($router) {
            $this->renderApi($path[0], 'PUT', array_slice($path, 1),
                $router->request->put());
        });
        $router->get('/api/:path+', function ($path) use ($router) {
            $this->renderApi($path[0], 'DELETE', array_slice($path, 1), []);
        });
        $router->get('/:lang/', function ($lang) use ($controller) {
            $controller($lang, '/index');
        });
        $router->get('/', function () use ($controller) {
            $controller('default', '/index');
        });
        $router->get('/:lang/:path+', function ($lang, $path) use ($controller) {
            if ($path[count($path) - 1] === '') {
                $path[count($path) - 1] = 'index';
            }
            $path = implode('/', $path);
            $controller($lang, $path);
        });
        $router->get('/:path+', function ($path) use ($controller) {
            if ($path[count($path) - 1] === '') {
                $path[count($path) - 1] = 'index';
            }
            $path = implode('/', $path);
            $controller('default', $path);
        });
        $router->notFound(function () use ($controller) {
            $controller('default', '/error404');
        });
    }

    private function mergeParams($lang, $template_name, &$params)
    {
        $params['lang'] = $lang;

        $nav = new Navigation($this->_config);
        $params['global_nav'] = $nav->getGlobalNav($lang);
        $params['local_nav'] = $nav->getLocalNav($lang, $template_name);
        $params['news_nav'] = $nav->getNews($lang);
        $params['breadcrumb'] = $nav->getBreadcrumb($lang, $template_name);
        $params['link'] = $nav->getAlterNav($lang, $template_name);

        $params['bgimage'] = (new BgImage())->getRandom();

        return $params;
    }
}
