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

function routeAdminNews($router)
{
    $cntrllr = function () use ($router) {
        return $router
            ->getContainer()
            ->newInstance('Ranyuen\Controller\AdminNewsController');
    };
    $router->get('/admin/news/new', function () use ($cntrllr) {
        $cntrllr()->newNews();
    });
    $router->get('/admin/news/edit/:id', function ($id) use ($cntrllr) {
        $cntrllr()->edit($id);
    });
    $router->post('/admin/news/create', function () use ($cntrllr) {
        $cntrllr()->create();
    });
    $router->put('/admin/news/update/:id', function ($id) use ($cntrllr) {
        $cntrllr()->update($id);
    });
    $router->delete('/admin/news/destroy/:id', function ($id) use ($cntrllr) {
        $cntrllr()->destroy($id);
    });
}
routeAdminNews($router);

function routeAdmin($router)
{
    $controller = function () use ($router) {
        return $router
            ->getContainer()
            ->newInstance('Ranyuen\Controller\AdminController');
    };
    $router->get('/admin/', function () use ($controller) {
        $controller()->index();
    });
    $router->get('/admin/login', function () use ($controller) {
        $controller()->showLogin();
    });
    $router->post('/admin/login', function () use ($router, $controller) {
        $controller()->login(
            $router->request->post('username'),
            $router->request->post('password')
        );
    });
    $router->map('/admin/logout', function () use ($controller) {
        $controller()->logout();
    })->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH');
    $router->map('/admin/*', function () use ($router) {
        $router->notFound();
    })->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH');
}
routeAdmin($router);

function routeNews($router)
{
    $c = $router->getContainer();
    $langRegex = implode('|', $c['nav']->getLangs());
    $controller = function () use ($c) {
        $renderer = $c['renderer'];
        $renderer->addHelper($c->newInstance('Ranyuen\Helper\ArticleHelper'));

        return $c->newInstance(
            'Ranyuen\Controller\NewsController',
            ['renderer' => $renderer]
        );
    };
    $router->get('/news/', function () use ($controller) {
        $controller()->index();
    });
    $router->get('/:lang/news/', function ($lang) use ($controller) {
        $controller()->index($lang);
    })->conditions(['lang' => $langRegex]);
    $router->get('/news/:path+', function ($path) use ($controller) {
        if (is_array($path)) {
            $path = implode('/', $path);
        }
        $controller()->show($path);
    });
    $router->get('/:lang/news/:path+', function ($lang, $path) use ($controller) {
        if (is_array($path)) {
            $path = implode('/', $path);
        }
        $controller()->show($path, $lang);
    })->conditions(['lang' => $langRegex]);
    $router->map('/news/*', function () use ($router) {
        $router->notFound();
    })->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH');
    $router->map('/:lang/news/*', function ($lang) use ($router) {
        $router->notFound();
    })
        ->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH')
        ->conditions(['lang' => $langRegex]);
}
routeNews($router);

function routeNavPhotos($router)
{
    $nav = $router->getContainer()['nav'];
    $langRegex = implode('|', $nav->getLangs());
    $router->get('/photos/*', function () use ($router) {
        $router->getContainer()
            ->newInstance('Ranyuen\Controller\NavPhotosController')
            ->showFromTemplate('default');
    });
    $router->get('/:lang/photos/*', function ($lang) use ($router) {
        $router->getContainer()
            ->newInstance('Ranyuen\Controller\NavPhotosController')
            ->showFromTemplate($lang);
    })->conditions(['lang' => $langRegex]);
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
    $langRegex = implode('|', $nav->getLangs());
    $router->notFound(function () use ($controller) {
        $controller('default', '/error404');
    });
    $router->get('/:lang/', function ($lang) use ($controller) {
        $controller($lang, '/index');
    })->conditions(['lang' => $langRegex]);
    $router->get('/', function () use ($controller) {
        $controller('default', '/index');
    });
    $router->get('/:lang/:path+', function ($lang, $path) use ($controller) {
        if ('' === $path[count($path) - 1]) {
            $path[count($path) - 1] = 'index';
        }
        $path = implode('/', $path);
        $controller($lang, $path);
    })->conditions(['lang' => $langRegex]);
    $router->get('/:path+', function ($path) use ($controller) {
        if ('' === $path[count($path) - 1]) {
            $path[count($path) - 1] = 'index';
        }
        $path = implode('/', $path);
        $controller('default', $path);
    });
}
routeNav($router);
