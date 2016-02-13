<?php

use Ranyuen\App;
use Ranyuen\Little\Request;
use Ranyuen\Little\Response;
use Ranyuen\Little\Router;
use Ranyuen\Model\Article;
use Ranyuen\Template\MainViewRenderer;
use Ranyuen\Template\ViewRenderer;

Router::plugin('Ranyuen\Little\Plugin\ControllerAnnotationRouter');

$router->error(500, function (\Exception $ex) {
    if ($ex instanceof \Ranyuen\Controller\Http403ForbiddenException) {
        $res = '403 Forbidden.';
        if ($ex->redirectUri) {
            $res .= ' <a href="'.h($ex->redirectUri).'">Go to '.h($ex->redirectUri).'.</a>';
        }

        return new Response($res, 403);
    }

    return new Response((string) $ex, 500);
});

$router->error(404, function (ViewRenderer $renderer, $lang) {
    return new Response('404 Not Found', 404);
    // $res = $renderer->render("error404.$lang");

    // return new Response($res, 404);
});

$router->registerController('Ranyuen\Controller\ApiPhotoController');
$router->registerController('Ranyuen\Controller\AdminArticlesController');
$router->registerController('Ranyuen\Controller\AdminController');

$router->get('/columns/', function (ViewRenderer $renderer, $nav, $bgimage, $config) {
    $renderer = new MainViewRenderer($renderer, $nav, $bgimage, $config);
    $params = $renderer->defaultParams('ja', '/columns/');
    $params['articles'] = array_reverse(Article::children('/columns/'));

    return $renderer->render('columns/list.ja', $params);
});

$router->get('/news/', function (ViewRenderer $renderer, $nav, $bgimage, $config) {
    $renderer = new MainViewRenderer($renderer, $nav, $bgimage, $config);
    $params = $renderer->defaultParams('ja', '/news/');
    $params['articles'] = Article::children('/news/');

    return $renderer->render('news/list.ja', $params);
});

$router->get('/photos/', function (App $app, Request $req, $lang, ViewRenderer $renderer, $nav, $bgimage, $config) {
    $controller = $app->container->newInstance('Ranyuen\Controller\ApiPhotoController');
    $speciesName = $req->get('species_name');
    $photos = $controller->photos($req, 0, 30);
    $photos = array_map(
        function ($photo) {
            $thumbWidth = 349;
            $thumbHeight = floor($photo['height'] * $thumbWidth / $photo['width']);
            $photo['thumb_width']  = $thumbWidth;
            $photo['thumb_height'] = $thumbHeight;

            return $photo;
        },
        json_decode($photos->getContent(), true)
    );
    $renderer = new MainViewRenderer($renderer, $nav, $bgimage, $config);
    $params = $renderer->defaultParams($lang, $req->getPathInfo());
    $params['species_name'] = $speciesName;
    $params['photos']       = $photos;

    return $renderer->render("photos/index.$lang", $params);
});

$router->registerController('Ranyuen\Controller\ArticleController');
