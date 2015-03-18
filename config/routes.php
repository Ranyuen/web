<?php

use Ranyuen\App;
use Ranyuen\Little\Request;
use Ranyuen\Little\Response;
use Ranyuen\Little\Router;
use Ranyuen\Template\ViewRenderer;

// use Ranyuen\FrozenResponse;

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

$router->error(404, function (ViewRenderer $renderer, $lang) {
    return new Response('404 Not Found', 404);
    // $res = $renderer->render("error404.$lang");

    // return new Response($res, 404);
});

$router->registerController('Ranyuen\Controller\ApiPhotoController');
$router->registerController('Ranyuen\Controller\AdminArticlesController');
$router->registerController('Ranyuen\Controller\AdminController');
$router->get('/photos/', function (App $app, Request $req, ViewRenderer $renderer, $lang) {
    $controller = $app->container->newInstance('Ranyuen\Controller\ApiPhotoController');
    $speciesName = $req->get('species_name');
    $photos = $controller->photos($req, 0, 20);
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

    return $app->container->newInstance('Ranyuen\Controller\ArticleController')->render(
        $lang,
        '/photos/',
        [
            'species_name' => $speciesName,
            'photos'       => $photos,
        ]
    );
});
$router->registerController('Ranyuen\Controller\ArticleController');
