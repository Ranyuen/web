<?php
/**
 * Ranyuen web site.
 */

namespace Ranyuen\Controller;

use Ranyuen\Template\MainViewRenderer;
use Ranyuen\Model\Article;
use Ranyuen\Model\ArticleContent;
use Ranyuen\Little\Request;
use Ranyuen\Little\Response;
use Ranyuen\Little\Router;

/**
 * Static pages.
 * @Route('/')
 */
class ArticleController extends Controller
{
    /**
     * @var Ranyuen\Navigation
     * @Inject
     */
    protected $nav;
    /**
     * @var Ranyuen\Template\ViewRenderer
     * @Inject
     */
    private $renderer;
    /**
     * @var Ranyuen\BgImage
     * @Inject
     */
    protected $bgimage;

    /**
     * @Route(':path*?')
     */
    public function show(Router $router, Request $req, $lang, $path = '')
    {
        $path = '/'.$path;
        foreach ($this->config['redirect'] as $from => $to) {
            if ($path === $from) {
                return new Response('', 301, ['Location' => $to]);
            }
        }
        if (!(
            ($article = Article::findByPath($path))
            && ($content = $article->getContent($lang))
        )) {
            return $router->error(404, $req);
        }
        $renderer = new MainViewRenderer($this->renderer, $this->nav, $this->bgimage, $this->config);
        $params = $renderer->defaultParams($lang, $path);

        return $renderer->renderContent($content->content, $params);
    }
}
