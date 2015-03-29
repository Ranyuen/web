<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Controller;

use Ranyuen\Little\Request;
use Ranyuen\Little\Response;
use Ranyuen\Little\Router;
use Ranyuen\Model\Article;
use Ranyuen\Model\ArticleContent;
use Ranyuen\Template\Template;

/**
 * Admin articles.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @Route('/admin/articles')
 */
class AdminArticlesController extends AdminController
{
    /**
     * Edit article.
     *
     * @param Router  $router Router.
     * @param Request $req    HTTP request.
     * @param string  $id     News ID.
     *
     * @return string
     *
     * @Route('/edit/:id')
     */
    public function edit(Router $router, Request $req, $id)
    {
        $this->auth();
        $id -= 0;
        if (0 === $id) {
            $article = null;
        } else {
            if (!($article = Article::with('contents')->find($id))) {
                return $router->error(404, $req);
            }
        }
        $json = json_encode($article);
        $json = str_replace('\\"', '\\\\"', $json);
        $json = str_replace('\\s', '\\\\\\s', $json);
        // $json = preg_replace('#\\[^nr]#', '\\\\\\$0', $json);
        return $this->renderer->render(
            'admin/articles/edit',
            ['article' => $json]
        );
    }

    /**
     * Update the article.
     *
     * @param Router  $router  Router.
     * @param Request $req     HTTP request.
     * @param string  $id      News ID.
     * @param string  $article JSON.
     *
     * @return string|Response
     *
     * @Route('/update/:id',via=PUT)
     */
    public function update(Router $router, Request $req, $id, $article)
    {
        $this->auth();
        $id         = intval($id);
        $article    = json_decode($article);
        $newArticle = new Article(['path' => $article->path]);
        $newArticle->contents = array_map(
            function ($content) {
                return new ArticleContent(
                    [
                    'lang'    => $content->lang,
                    'content' => $content->content,
                    ]
                );
            },
            $article->contents
        );
        if (0 === $id) {
            $original = new Article();
        } else {
            if (!$original = Article::with('contents')->find($id)) {
                return $router->error(404, $req);
            }
        }
        $original->sync($newArticle);

        return json_encode($original);
    }

    /**
     * Destroy the article.
     *
     * @param string $id News ID.
     *
     * @return Response
     *
     * @Route('/destroy/:id',via=DELETE)
     */
    public function destroy($id)
    {
        $this->auth();
        Article::destroy($id);

        return new Response('', 303, ['Location' => '/admin/']);
    }

    /**
     * Preview content.
     *
     * @param string $content ArticleContent.
     *
     * @return Response
     *
     * @Route('/preview',via=POST)
     */
    public function preview($content)
    {
        return (new Template($content))->render();
    }
}
