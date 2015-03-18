<?php
/**
 * Ranyuen web site.
 */

namespace Ranyuen\Controller;

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
     * @var Ranyuen\Template\ViewRenderer
     * @Inject
     */
    private $renderer;

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
        $params = $this->defaultParams($lang, $req->getPathInfo());

        return $this->renderer->renderContent($content->content, $params);
    }

    /**
     * @param string $lang Current lang.
     * @param string $path Template path.
     *
     * @return array
     */
    private function defaultParams($lang, $path)
    {
        if (isset($this->config['lang'][$lang])) {
            $lang = $this->config['lang'][$lang];
        }
        $nav = $this->nav;

        return [
            'lang'       => $lang,
            'nav'        => [
                'global' => $nav->getGlobalNav($lang),
                'local'  => $nav->getLocalNav($lang, $path),
            ],
            'breadcrumb' => $nav->getBreadcrumb($lang, $path),
            'link'       => $nav->getAlterNav($lang, $path),
            'bgimage'    => $this->bgimage->getRandom(),
            'messages'   => $this->config['message'][$lang],
        ];
    }
}
