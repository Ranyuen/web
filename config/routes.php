<?php
$router->map('/api/:path+', function ($path) use ($router) {
    $router->getContainer()
        ->newInstance('\Ranyuen\Controller\ApiController')
        ->renderApi(
            $path[0],
            $router->request->getMethod(),
            array_slice($path, 1),
            $router->request->params()
        );
})->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH');

function routeNavPhotos($router)
{
    $nav = $router->getContainer()['nav'];
    $lang_regex = implode('|', $nav->getLangs());
    $router->get('/photos/*', function () use ($router) {
        $router->getContainer()
            ->newInstance('\Ranyuen\Controller\NavPhotosController')
            ->showFromTemplate('default');
    });
    $router->get('/:lang/photos/*', function ($lang) use ($router) {
        $router->getContainer()
            ->newInstance('\Ranyuen\Controller\NavPhotosController')
            ->showFromTemplate($lang);
    })->conditions(['lang' => $lang_regex]);
}

routeNavPhotos($router);

function routeNav($router)
{
    $nav = $router->getContainer()['nav'];
    $controller = function ($lang, $path) use ($router) {
        $router->getContainer()
            ->newInstance('\Ranyuen\Controller\NavController')
            ->showFromTemplate($lang, $path);
    };
    $lang_regex = implode('|', $nav->getLangs());
    $router->notFound(function () use ($controller) {
        $controller('default', '/error404');
    });
    $router->get('/:lang/', function ($lang) use ($controller) {
        $controller($lang, '/index');
    })->conditions(['lang' => $lang_regex]);
    $router->get('/', function () use ($controller) {
        $controller('default', '/index');
    });
    $router->get('/:lang/:path+', function ($lang, $path) use ($controller) {
        if ($path[count($path) - 1] === '') {
            $path[count($path) - 1] = 'index';
        }
        $path = implode('/', $path);
        $controller($lang, $path);
    })->conditions(['lang' => $lang_regex]);
    $router->get('/:path+', function ($path) use ($controller) {
        if ($path[count($path) - 1] === '') {
            $path[count($path) - 1] = 'index';
        }
        $path = implode('/', $path);
        $controller('default', $path);
    });
}

routeNav($router);
