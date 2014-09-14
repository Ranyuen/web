<?php
namespace Ranyuen;

use \Slim;

/**
 * Quick start
 * ===========
 * ```php
 * (new \Ranyuen\App([]))->run();
 * ```
 */
class App
{
    /** @type \Slim\Slim */
    public $app;

    /** @type \Ranyuen\Logger */
    public $logger;

    private $config;

    /**
     * @param array $config
     */
    public function __construct($config = null)
    {
        session_start();
        $env = isset($_ENV['SERVER_ENV']) ? $_ENV['SERVER_ENV'] : 'development';
        if ($env === 'development') { ini_set('display_errors', 1); }
        if (is_null($config)) { $config = (new Config())->load("config/$env.yaml"); }
        $this->app = new Slim\Slim();
        $this->config = $this->setDefaultConfig($config, $env);
        $this->app->config($this->config);
        $this->applyDefaultRoutes($this->app);
        $this->logger = new Logger($this->config['mode'], $this->config);
    }

    /**
     * @return App
     */
    public function run()
    {
        $this->app->run();

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
        $renderer = new Renderer($this->config);
        if (isset($this->config['lang'][$lang])) {
            $lang = $this->config['lang'][$lang];
        }
        $this->mergeParams($lang, $template_name, $params);
        $str = $renderer
            ->setLayout($this->config['layout'])
            ->render("$template_name.$lang", $params);
        if ($str === false) {
            $this->app->notFound();
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
     * @param  array  $config
     * @param  string $env
     * @return array
     */
    private function setDefaultConfig($config, $env)
    {
        $set_default = function (&$array, $key, $dafault) {
            if (! isset($array[$key])) { $array[$key] = $dafault; }
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
        $set_default($config, 'log.level', \Slim\LOG::INFO);
        $set_default($config, 'mode', $env);
        $set_default($config, 'templates.path', 'templates');

        return $config;
    }

    /**
     * @param \Slim\Slim
     */
    private function applyDefaultRoutes($app)
    {
        $this->setDefaultRouteConditions($this->config);
        $controller = function ($lang, $path) use ($app) {
            foreach ($this->config['redirect'] as $src => $dest) {
                if ($_SERVER['REQUEST_URI'] === $src) {
                    $app->redirect($dest, 301);
                }
            }
            $this->render($lang, $path);
            $this->logger->addAccessInfo();
        };
        $app->get('/api/:path+', function ($path) use ($app) {
            $this->renderApi($path[0], 'GET', array_slice($path, 1),
                $app->request->get());
        });
        $app->get('/api/:path+', function ($path) use ($app) {
            $this->renderApi($path[0], 'POST', array_slice($path, 1),
                $app->request->post());
        });
        $app->get('/api/:path+', function ($path) use ($app) {
            $this->renderApi($path[0], 'PUT', array_slice($path, 1),
                $app->request->put());
        });
        $app->get('/api/:path+', function ($path) use ($app) {
            $this->renderApi($path[0], 'DELETE', array_slice($path, 1), []);
        });
        $app->get('/:lang/', function ($lang) use ($controller) {
            $controller($lang, '/index');
        });
        $app->get('/', function () use ($controller) {
            $controller('default', '/index');
        });
        $app->get('/:lang/:path+', function ($lang, $path) use ($controller) {
            if ($path[count($path) - 1] === '') {
                $path[count($path) - 1] = 'index';
            }
            $path = implode('/', $path);
            $controller($lang, $path);
        });
        $app->get('/:path+', function ($path) use ($controller) {
            if ($path[count($path) - 1] === '') {
                $path[count($path) - 1] = 'index';
            }
            $path = implode('/', $path);
            $controller('default', $path);
        });
        $app->notFound(function () use ($controller) {
            $controller('default', '/error404');
        });
    }

    /**
     * @param array $config
     */
    private function setDefaultRouteConditions($config)
    {
        $langs = (new Navigation($this->config))->getLangs();
        $lang_regex = implode('|', $langs);
        Slim\Route::setDefaultConditions([
            'lang' => $lang_regex
        ]);
    }

    private function mergeParams($lang, $template_name, &$params)
    {
        $params['lang'] = $lang;

        $nav = new Navigation($this->config);
        $params['global_nav'] = $nav->getGlobalNav($lang);
        $params['local_nav'] = $nav->getLocalNav($lang, $template_name);
        $params['news_nav'] = $nav->getNews($lang);
        $params['breadcrumb'] = $nav->getBreadcrumb($lang, $template_name);
        $params['link'] = $nav->getAlterNav($lang, $template_name);

        $params['bgimage'] = (new BgImage())->getRandom();

        return $params;
    }
}
