<?php
$connector = $router->getContainer()
    ->newInstance(
        'Ranyuen\Controller\Connector',
        [$router->getContainer()]
    );

$router->map('/api/:path+', function ($path) use ($router) {
    $apiName = preg_replace_callback(
        '/[-_](.)/',
        function ($m) {
            return strtoupper($m[1]);
        },
        ucwords(strtolower($path[0]))
    );
    $cntrllr = $router
        ->getContainer()
        ->newInstance('Ranyuen\Controller\Api'.$apiName.'Controller');
    $params = array_merge(array_slice($path, 1), $router->request->params());
    $cntrllr->render($router->request->getMethod(), $params);
})->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH');

function routeAdminNews($rtr, $cnnctr)
{
    $rtr->get('/admin/news/new', function () use ($cnnctr) {
        $cnnctr->invoke('AdminNews', 'make');
    });
    $rtr->get('/admin/news/edit/:id', function ($id) use ($cnnctr) {
        $cnnctr->invoke('AdminNews', 'edit', ['id' => $id]);
    });
    $rtr->post('/admin/news/create', function () use ($cnnctr) {
        $cnnctr->invoke('AdminNews', 'create');
    });
    $rtr->put('/admin/news/update/:id', function ($id) use ($cnnctr) {
        $cnnctr->invoke('AdminNews', 'update', ['id' => $id]);
    });
    $rtr->delete('/admin/news/destroy/:id', function ($id) use ($cnnctr) {
        $cnnctr->invoke('AdminNews', 'destroy', ['id' => $id]);
    });
}
routeAdminNews($router, $connector);

function routeAdminNewsTag($rtr, $cnnctr)
{
    $rtr->get('/admin/news_tag/new', function () use ($cnnctr) {
        $cnnctr->invoke('AdminNewsTag', 'make');
    });
    $rtr->get('/admin/news_tag/edit/:id', function ($id) use ($cnnctr) {
        $cnnctr->invoke('AdminNewsTag', 'edit', ['id' => $id]);
    });
    $rtr->post('/admin/news_tag/create', function () use ($cnnctr) {
        $cnnctr->invoke('AdminNewsTag', 'create');
    });
    $rtr->put('/admin/news_tag/update/:id', function ($id) use ($cnnctr) {
        $cnnctr->invoke('AdminNewsTag', 'update', ['id' => $id]);
    });
    $rtr->delete('/admin/news_tag/destroy/:id', function ($id) use ($cnnctr) {
        $cnnctr->invoke('AdminNewsTag', 'destroy', ['id' => $id]);
    });
}
routeAdminNewsTag($router, $connector);

function routeAdmin($rtr, $cnnctr)
{
    $rtr->get('/admin/', function () use ($cnnctr) {
        $cnnctr->invoke('Admin', 'index');
    });
    $rtr->get('/admin/login', function () use ($cnnctr) {
        $cnnctr->invoke('Admin', 'showLogin');
    });
    $rtr->post('/admin/login', function () use ($cnnctr, $rtr) {
        $cnnctr->invoke('Admin', 'login', $rtr->request->post());
    });
    $rtr->map('/admin/logout', function () use ($cnnctr) {
        $cnnctr->invoke('Admin', 'logout');
    })->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH');
    $rtr->map('/admin/*', function () use ($rtr) {
        $rtr->notFound();
    })->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH');
}
routeAdmin($router, $connector);

function routeNews($rtr)
{
    $c = $rtr->getContainer();
    $langRegex = implode('|', $c['nav']->getLangs());
    $cntrllr = function () use ($c) {
        $renderer = $c['renderer'];
        $renderer->addHelper($c->newInstance('Ranyuen\Helper\ArticleHelper'));

        return $c->newInstance(
            'Ranyuen\Controller\NewsController',
            ['renderer' => $renderer]
        );
    };
    $rtr->get('/news/', function () use ($cntrllr) {
        $cntrllr()->index();
    });
    $rtr->get('/:lang/news/', function ($lang) use ($cntrllr) {
        $cntrllr()->index($lang);
    })->conditions(['lang' => $langRegex]);
    $rtr->get('/news/list', function () use ($cntrllr) {
        $cntrllr()->lists();
    });
    $rtr->get('/:lang/news/list', function ($lang) use ($cntrllr) {
        $cntrllr()->lists($lang);
    })->conditions(['lang' => $langRegex]);
    $rtr->get('/news/:path+', function ($path) use ($cntrllr) {
        if (is_array($path)) {
            $path = implode('/', $path);
        }
        $cntrllr()->show($path);
    });
    $rtr->get('/:lang/news/:path+', function ($lang, $path) use ($cntrllr) {
        if (is_array($path)) {
            $path = implode('/', $path);
        }
        $cntrllr()->show($path, $lang);
    })->conditions(['lang' => $langRegex]);
    $rtr->map('/news/*', function () use ($rtr) {
        $rtr->notFound();
    })->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH');
    $rtr->map('/:lang/news/*', function ($lang) use ($rtr) {
        $rtr->notFound();
    })
        ->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH')
        ->conditions(['lang' => $langRegex]);
}
routeNews($router);

function routeNavPhotos($rtr, $cnnctr)
{
    $langRegex = implode('|', $rtr->getContainer()['nav']->getLangs());
    $rtr->get('/photos/*', function () use ($cnnctr) {
        $cnnctr->invoke('NavPhotos', 'showFromTemplate', ['lang' => 'default']);
    });
    $rtr->get('/:lang/photos/*', function ($lang) use ($rtr) {
        $cnnctr->invoke('NavPhotos', 'showFromTemplate', ['lang' => $lang]);
    })->conditions(['lang' => $langRegex]);
}
routeNavPhotos($router, $connector);

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
