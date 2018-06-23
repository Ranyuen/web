<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Controller;

use Ranyuen\Template\MainViewRenderer;
use Ranyuen\Template\ViewRenderer;
use Ranyuen\Model\Article;
use Ranyuen\Model\Photo;
use Ranyuen\Little\Request;
use Ranyuen\Little\Response;
use Ranyuen\Little\Router;
use Illuminate\Database\Eloquent;
use Strana\ConfigHelper;
use Strana\Interfaces\CollectionAdapter;

/**
 *  Exam.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class PhotoController extends Controller
{
    /**
     * Renderer.
     *
     * @var Ranyuen\Template\ViewRenderer
     *
     * @Inject
     */
    protected $renderer;

    /**
     * Navigation.
     *
     * @var Ranyuen\Navigation
     *
     * @Inject
     */
    protected $nav;

    /**
     * BgImage.
     *
     * @var Ranyuen\BgImage
     *
     * @Inject
     */
    protected $bgimage;

    /**
     * Photos.
     *
     * @param Request $req    HTTP request.
     *
     * @return Renderer
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @Route('/photos/')
     */
    public function index(Request $req, $lang, ViewRenderer $renderer, $nav, $bgimage, $config) {
        $controller  = (new \Ranyuen\App())->container->newInstance('Ranyuen\Controller\ApiPhotoController');
        $speciesName = $req->get('species_name');
        $color       = $req->get('color');
        $photos      = $controller->photos($req, 0, 500);
        $photos      = array_map(
            function ($photo) {
                $thumbWidth            = 349;
                $thumbHeight           = floor($photo['height'] * $thumbWidth / $photo['width']);
                $photo['thumb_width']  = $thumbWidth;
                $photo['thumb_height'] = $thumbHeight;

                return $photo;
            },
            json_decode($photos->getContent(), true)
        );

        $records   = $photos;
        $strana    = new \Strana\Paginator();
        $paginator = $strana->perPage(20)->make($records, null, array('maximumPages' => 10));
        $renderer  = new MainViewRenderer($renderer, $nav, $bgimage, $config);
        $params    = $renderer->defaultParams($lang, $req->getPathInfo());
        if (!is_null($speciesName)) {
            $params['colors'] = Photo::where('species_name', $speciesName)
                ->whereNotNull('color')->distinct()->get(['color']);
        }
        $params['select_color'] = $color;
        $params['species_name'] = $speciesName;
        $params['photos']       = $photos;
        $params['paginator']    = $paginator;

        return $renderer->render("photos/index.$lang", $params);
    }
}
