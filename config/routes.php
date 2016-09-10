<?php

use Ranyuen\App;
use Ranyuen\Little\Request;
use Ranyuen\Little\Response;
use Ranyuen\Little\Router;
use Ranyuen\Model\Article;
use Ranyuen\Model\ExamQuestion;
use Ranyuen\Template\MainViewRenderer;
use Ranyuen\Template\ViewRenderer;



//
use Strana\ConfigHelper;
use Strana\Interfaces\CollectionAdapter;

class CustomAdapter implements CollectionAdapter{

    /**
     * @var \Strana\ConfigHelper
     * Config helper is a helper class, which gives you config values
     *  used by Strana.
     */
    protected $configHelper;

    /**
     * @var
     */
    protected $records;

    public function __construct($records, ConfigHelper $configHelper)
    {
        $this->records = $records;
        $this->configHelper = $configHelper;
    }

    /**
     * This method should limit and offset your records and return.
     */
    public function slice()
    {
        // Here you will get the database object passed to Strana.
        //  Clone it.
        $records = clone($this->records);

        // Get the limit number from Strana config
        $limit = $this->configHelper->getLimit();

        // Get the offset number from Strana config
        $offset = $this->configHelper->getOffset();

        // Limit your records
        $records->limit($limit);
        // Offset your records
        $records->offset($offset);

        // Return your sliced records
        return $records->get();
    }

    /**
     * This method should return total count of all of your records.
     */
    public function total()
    {
        // Here you will get the database object passed to Strana.
        //  Clone it.
        $records = clone($this->records);

        // Return your total records count, unsliced.
        return $records->count();
    }
}
//

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
$router->registerController('Ranyuen\Controller\ExamController');

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
    // $photos = $controller->photos($req, 0, 1000);
    $photos = $controller->photos($req, 0, 2000);
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


    $records = $photos;
    $strana = new \Strana\Paginator();

    $paginator = $strana->perPage(20)->make($records, null, $config);

    $renderer = new MainViewRenderer($renderer, $nav, $bgimage, $config);

    $params = $renderer->defaultParams($lang, $req->getPathInfo());

    $params['species_name'] = $speciesName;
    $params['photos']       = $photos;
    $params['paginator']    = $paginator;

    return $renderer->render("photos/index.$lang", $params);


});

$router->registerController('Ranyuen\Controller\ArticleController');
