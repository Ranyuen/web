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

$router->get('/columns/', function (Request $req) {
    return new Response('', 302, ['Location' => '/news/list?tag=Column']);
});

$router->registerController('Ranyuen\Controller\ApiPhotoController');
$router->registerController('Ranyuen\Controller\AdminNewsController');
$router->registerController('Ranyuen\Controller\AdminNewsTagController');
$router->registerController('Ranyuen\Controller\AdminController');
$router->registerController('Ranyuen\Controller\NewsController');
$router->registerController('Ranyuen\Controller\NavController');
