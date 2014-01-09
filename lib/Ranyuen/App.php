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
     * @param string|array $config
     */
    public function __construct($config = 'development')
    {
        session_start();
        $env = isset($_ENV['SERVER_ENV']) ? $_ENV['SERVER_ENV'] : 'development';
        if (is_string($config)) {
            $env = $config;
            $config = (new Config)->load("config/$config.yaml");
        }
        if ($env === 'development') { ini_set('display_errors', 1); }
        $this->app = new Slim\Slim;
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
        echo $renderer
            ->setLayout($this->config['layout'])
            ->render("$template_name.$lang", $params);

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
        $controller = function ($lang, $path) {
            $this->render($lang, $path);
            $this->logger->addAccessInfo();
        };
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
    }

    /**
     * @param array $config
     */
    private function setDefaultRouteConditions($config)
    {
        $langs = [];
        $dir = $config['templates.path'];
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if (is_dir($dir . $file)) { $langs[] = $file; }
            }
        }
        $langs = array_unique(array_merge($langs, array_keys($config['lang'])));
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

        $params['bgimage'] = (new BgImage)->getRandom();
    }
}
