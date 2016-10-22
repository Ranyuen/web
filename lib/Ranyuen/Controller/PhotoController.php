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
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @Route('/photos/')
     */
    public function index() {
        $renderer = new MainViewRenderer($this->renderer, $this->nav, $this->bgimage, $this->config);
        $params = $renderer->defaultParams('ja', '/photos/');
        $colors = Photo::select('color')->get();

        // $params['colors'] = 'aaaaaa';
        // $article = Article::findByPath('/play/exam/' . $type . '/practice');
        // $content = $article->getContent('ja');

        // return $renderer->renderContent($content->content, $params);
        return $renderer->render('/photos/', $params);
    }
}
