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
use Ranyuen\Model\Article;
use Ranyuen\Little\Request;
use Ranyuen\Little\Response;
use Ranyuen\Little\Router;

/**
 * Static pages.
 *
 * @Route('/')
 */
class ArticleController extends Controller
{
    /**
     * Navigation.
     *
     * @var Ranyuen\Navigation
     *
     * @Inject
     */
    protected $nav;
    /**
     * Renderer.
     *
     * @var Ranyuen\Template\ViewRenderer
     *
     * @Inject
     */
    private $renderer;
    /**
     * BgImage.
     *
     * @var Ranyuen\BgImage
     *
     * @Inject
     */
    protected $bgimage;

    /**
     * Show the article.
     *
     * @param Router  $router Router.
     * @param Request $req    HTTP request.
     * @param string  $lang   Lang.
     * @param string  $path   URI path info.
     *
     * @return Response
     *
     * @Route(':path*?')
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function show(Router $router, Request $req, $lang, $path = '')
    {
        $path = '/'.$path;
        foreach ($this->config['redirect'] as $from => $to) {
            if ($path === $from) {
                return new Response('', 301, ['Location' => $to]);
            }
        }
        if (preg_match('#\A/news/(.+)#', $path, $matches)
            && $article = Article::findByPath("/columns/$matches[1]")
        ) { // For migrating #94.
            return new Response('', 301, ['Location' => "/columns/$matches[1]"]);
        }
        if (!(($article = Article::findByPath($path))
            && ($content = $article->getContent($lang)))
        ) {
            return $router->error(404, $req);
        }
        $renderer = new MainViewRenderer($this->renderer, $this->nav, $this->bgimage, $this->config);
        $params = $renderer->defaultParams($lang, $path);

        return $renderer->renderContent($content->content, $params);
    }
}
