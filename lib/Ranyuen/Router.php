<?php
namespace Ranyuen;

use \ReflectionClass;
use \Slim;

class Router extends Slim\Slim
{
    /** @var App */
    private $_app;

    /**
     * @param App        $app
     * @param Navigation $nav
     * @param Logger     $logger
     * @param array      $config
     */
    public function __construct(App $app, array $config)
    {
        parent::__construct();
        $this->config($config);
        $this->_app = $app;
    }

    /**
     * Override.
     */
    public function run()
    {
        $methods = (new ReflectionClass(get_class($this)))->getMethods();
        foreach ($methods as $method) {
            if (preg_match('/@routing/', $method->getDocComment()) >= 1) {
                $method->setAccessible(true);
                $method->invoke($this, $this->_app);
            }
        }
        parent::run();
    }

    /** @routing */
    private function routeApi(App $app)
    {
        $this->map('/api/:path+', function ($path) use ($app) {
            $app->getContainer()
                ->newInstance('\Ranyuen\Controller\ApiController')
                ->renderApi(
                    $path[0],
                    $this->request->getMethod(),
                    array_slice($path, 1),
                    $this->request->params()
                );
        })->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH');
    }

    /** @routing */
    private function routeNavPhotos(App $app)
    {
        $nav = $app->getContainer()['nav'];
        $lang_regex = implode('|', $nav->getLangs());
        $this->get('/photos/*', function () use ($app) {
            $app->getContainer()
                ->newInstance('\Ranyuen\Controller\NavPhotosController')
                ->showFromTemplate('default');
        });
        $this->get('/:lang/photos/*', function ($lang) use ($app) {
            $app->getContainer()
                ->newInstance('\Ranyuen\Controller\NavPhotosController')
                ->showFromTemplate($lang);
        })->conditions(['lang' => $lang_regex]);
    }

    /** @routing */
    private function routeNav(App $app)
    {
        $nav = $app->getContainer()['nav'];
        $controller = function ($lang, $path) use ($app) {
            $app->getContainer()
                ->newInstance('\Ranyuen\Controller\NavController')
                ->showFromTemplate($lang, $path);
        };
        $lang_regex = implode('|', $nav->getLangs());
        $this->notFound(function () use ($controller) {
            $controller('default', '/error404');
        });
        $this->get('/:lang/', function ($lang) use ($controller) {
            $controller($lang, '/index');
        })->conditions(['lang' => $lang_regex]);
        $this->get('/', function () use ($controller) {
            $controller('default', '/index');
        });
        $this->get('/:lang/:path+', function ($lang, $path) use ($controller) {
            if ($path[count($path) - 1] === '') {
                $path[count($path) - 1] = 'index';
            }
            $path = implode('/', $path);
            $controller($lang, $path);
        })->conditions(['lang' => $lang_regex]);
        $this->get('/:path+', function ($path) use ($controller) {
            if ($path[count($path) - 1] === '') {
                $path[count($path) - 1] = 'index';
            }
            $path = implode('/', $path);
            $controller('default', $path);
        });
    }
}
