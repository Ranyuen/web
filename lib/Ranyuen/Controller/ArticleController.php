<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Controller;

use Ranyuen\App;
use Ranyuen\Model\Article;
use Ranyuen\Little\Request;
use Ranyuen\Little\Response;
use Ranyuen\Little\Router;

/**
 * Static pages
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
        foreach ($this->config['redirect'] as $from => $to) {
            if ($path === $from) {
                return new Response('', 301, ['Location' => $to]);
            }
        }
        $article = Article::where(['path' => $path])->first();
        if (!$article) {
            return $router->error(404, $req);
        }
        $content = $article->contents->where(['lang' => $lang])->first();
        if (!$content) {
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
