<?php

use Ranyuen\Little\Request;
use Ranyuen\Little\Response;
use Ranyuen\Little\Router;

Router::plugin('Ranyuen\Little\Plugin\ControllerAnnotationRouter');

$h = function ($text) {
    return htmlspecialchars(
        $text,
        ENT_QUOTES|ENT_DISALLOWED|ENT_HTML5,
        'UTF-8'
    );
};

$router->error(500, function (\Exception $ex) use ($h) {
    if ($ex instanceof \Ranyuen\Controller\Http403ForbiddenException) {
        $res = '403 Forbidden.';
        if ($ex->redirectUri) {
            $res .= ' <a href="'.$h($ex->redirectUri).'">Go to '.$h($ex->redirectUri).'.</a>';
        }

        return new Response($res, 403);
    }

    return new Response((string) $ex, 500);
});

$router->error(404, function () {
    return new Response('404 Not Found.', 404);
});

$router->map('/{lang}/*', function (Router $r, Request $req, $config, $lang) {
    if (isset($config['lang'][$lang])) {
        $lang = $config['lang'][$lang];
    }
    $req->query->set('lang', $lang);
    $req->server->set(
        'REQUEST_URI',
        substr($req->getPathInfo(), strlen($lang) + 1)
    );

    return $r->run($req);
})
    ->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH')
    ->assert('lang', '/\A(?:ja|en|e)\z/');

$router->get('/columns/', function (Request $req) {
    return new Response('', 302, ['Location' => '/news/list?tag=Column']);
});

$router->registerController('Ranyuen\Controller\ApiPhotoController');
$router->registerController('Ranyuen\Controller\AdminNewsController');
$router->registerController('Ranyuen\Controller\AdminNewsTagController');
$router->registerController('Ranyuen\Controller\AdminController');
$router->registerController('Ranyuen\Controller\NewsController');
$router->registerController('Ranyuen\Controller\NavController');
