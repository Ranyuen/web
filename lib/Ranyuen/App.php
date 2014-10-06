<?php
namespace Ranyuen;

use \Pimple\Container;

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
        $params = array_merge($uri_params, $request_params);
        switch ($method) {
        case 'GET':
            $response = $controller->get($params);
            break;
        }
        if (!$response) { return $this; }
        echo is_array($response) ? json_encode($response) : $response;

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
        $container['logger'] = function ($c) {
            $config = $c['config'];

            return new Logger($config['mode'], $config);
        };
        $container['nav'] = function ($c) {
            return new Navigation($c['config']);
        };
        $container['router'] = function ($c) {
            return new Router($this, $c['nav'], $c['logger'], $c['config']);
        };
        $container['db'] = function ($c) {
            return new DbCapsule($c['logger'], $c['config']['db']);
        };
        $this->_config = $container['config'];
        $this->_nav = $container['nav'];
        $this->_router = $container['router'];
    }

    private function mergeParams($lang, $template_name, &$params)
    {
        $params['lang'] = $lang;
        $params['global_nav'] = $this->_nav->getGlobalNav($lang);
        $params['local_nav'] = $this->_nav->getLocalNav($lang, $template_name);
        $params['news_nav'] = $this->_nav->getNews($lang);
        $params['breadcrumb'] = $this->_nav->getBreadcrumb($lang, $template_name);
        $params['link'] = $this->_nav->getAlterNav($lang, $template_name);
        $params['bgimage'] = (new BgImage())->getRandom();

        return $params;
    }
}
