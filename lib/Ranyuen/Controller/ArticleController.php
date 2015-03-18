<?php
/**
 * Ranyuen web site.
 */

namespace Ranyuen\Controller;

use Ranyuen\Model\Article;
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

        return $this->renderer->renderContent($content->content);
    }

    // private function render($lang, $templateName, array $params = [])
    // {
    //     $params = array_merge(
    //         $params,
    //         $this->getDefaultParams($lang, $templateName)
    //     );
    //     if ('/' === $templateName[strlen($templateName) - 1]) {
    //         $templateName .= 'index';
    //     }
    //     $res = $this->renderer->render("$templateName.$lang", $params);
    //     if (false === $res) {
    //         $res = $this->renderer->render("error404.$lang", $params);

    //         return new Response($res, 404);
    //     }

    //     return $res;
    // }
}
