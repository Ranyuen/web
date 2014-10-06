<?php
namespace Ranyuen;

use \Slim;

class Router extends Slim\Slim
{
    /**
     * @param App        $app
     * @param Navigation $nav
     * @param Logger     $logger
     * @param array      $config
     */
    public function __construct(App $app, Navigation $nav, Logger $logger, array $config)
    {
        parent::__construct();
        $this->config($config);
        $this->setDefaultRouteConditions($nav, $config);
        $this->applyDefaultRoutes($app, $logger, $config);
    }

    private function setDefaultRouteConditions(Navigation $nav, array $config)
    {
        $lang_regex = implode('|', $nav->getLangs());
        Slim\Route::setDefaultConditions(['lang' => $lang_regex]);
    }

    private function applyDefaultRoutes(App $app, Logger $logger, array $config)
    {
        $controller = function ($lang, $path) use ($app, $logger, $config) {
            foreach ($config['redirect'] as $src => $dest) {
                if ($_SERVER['REQUEST_URI'] === $src) {
                    $this->redirect($dest, 301);
                }
            }
            $app->render($lang, $path);
            $logger->addAccessInfo();
        };
        $this->get('/api/:path+', function ($path) use ($app) {
            $app->renderApi(
                $path[0],
                'GET',
                array_slice($path, 1),
                $this->request->get()
            );
        });
        $this->post('/api/:path+', function ($path) use ($app) {
            $app->renderApi(
                $path[0],
                'POST',
                array_slice($path, 1),
                $this->request->post()
            );
        });
        $this->put('/api/:path+', function ($path) use ($app) {
            $app->renderApi(
                $path[0],
                'PUT',
                array_slice($path, 1),
                $this->request->put()
            );
        });
        $this->delete('/api/:path+', function ($path) use ($app) {
            $app->renderApi($path[0], 'DELETE', array_slice($path, 1), []);
        });
        $this->patch('/api/:path+', function ($path) use ($app) {
            $app->renderApi($path[0], 'PATCH', array_slice($path, 1), []);
        });
        $this->get('/:lang/', function ($lang) use ($controller) {
            $controller($lang, '/index');
        });
        $this->get('/', function () use ($controller) {
            $controller('default', '/index');
        });
        $this->get('/:lang/:path+', function ($lang, $path) use ($controller) {
            if ($path[count($path) - 1] === '') {
                $path[count($path) - 1] = 'index';
            }
            $path = implode('/', $path);
            $controller($lang, $path);
        });
        $this->get('/:path+', function ($path) use ($controller) {
            if ($path[count($path) - 1] === '') {
                $path[count($path) - 1] = 'index';
            }
            $path = implode('/', $path);
            $controller('default', $path);
        });
        $this->notFound(function () use ($controller) {
            $controller('default', '/error404');
        });
    }
}
