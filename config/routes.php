<?php
$router->map('/api/:path+', function ($path) use ($router) {
    $router->getContainer()
        ->newInstance('Ranyuen\Controller\ApiController')
        ->renderApi(
            $path[0],
            $router->request->getMethod(),
            array_slice($path, 1),
            $router->request->params()
        );
})->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH');

function routeAdmin($router)
{
    $controller = $router->
        getContainer()->
        newInstance('Ranyuen\Controller\AdminController');
    $router->get('/admin/', function () use ($controller) {
        $controller->index();
    });
    $router->get('/admin/login', function () use ($controller) {
        $controller->showLogin();
    });
    $router->post('/admin/login', function () use ($router, $controller) {
        $controller->login(
            $router->request->post('username'),
            $router->request->post('password')
        );
    });
    $router->map('/admin/logout', function () use ($controller) {
        $controller->logout();
    })->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH');
    $router->map('/admin/*', function () use ($router) {
        $router->notFound();
    })->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH');
}
routeAdmin($router);

function routeNavPhotos($router)
{
    $nav = $router->getContainer()['nav'];
    $lang_regex = implode('|', $nav->getLangs());
    $router->get('/photos/*', function () use ($router) {
        $router->getContainer()
            ->newInstance('Ranyuen\Controller\NavPhotosController')
            ->showFromTemplate('default');
    });
    $router->get('/:lang/photos/*', function ($lang) use ($router) {
        $router->getContainer()
            ->newInstance('Ranyuen\Controller\NavPhotosController')
            ->showFromTemplate($lang);
    })->conditions(['lang' => $lang_regex]);
}
routeNavPhotos($router);

function routeNav($router)
{
    $nav = $router->getContainer()['nav'];
    $controller = function ($lang, $path) use ($router) {
        $router->getContainer()
            ->newInstance('Ranyuen\Controller\NavController')
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
        if ('' === $path[count($path) - 1]) {
            $path[count($path) - 1] = 'index';
        }
        $path = implode('/', $path);
        $controller($lang, $path);
    })->conditions(['lang' => $lang_regex]);
    $router->get('/:path+', function ($path) use ($controller) {
        if ('' === $path[count($path) - 1]) {
            $path[count($path) - 1] = 'index';
        }
        $path = implode('/', $path);
        $controller('default', $path);
    });
}
routeNav($router);
