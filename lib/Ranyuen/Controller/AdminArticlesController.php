<?php
/**
 * Ranyuen web site.
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
     * @param string $id News ID.
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

        return $this->renderer->render('admin/articles/edit', ['article' => str_replace('\\"', '\\\\"', json_encode($article))]);
    }

    /**
     * @param string $id News ID.
     *
     * @return string|Response
     *
     * @Route('/update/:id',via=PUT)
     */
    public function update(Router $router, Request $req, $id, $article)
    {
        $this->auth();
        $article = json_decode($article);
        $newArticle = new Article(['path' => $article->path]);
        $newArticle->contents = array_map(
            function ($content) {
                return new ArticleContent([
                    'lang'    => $content->lang,
                    'content' => $content->content,
                ]);
            },
            $article->contents
        );
        if (0 === $article->id) {
            $original = new Article();
        } else {
            if (!$original = Article::with('contents')->find($article->id)) {
                return $router->error(404, $req);
            }
        }
        $original->sync($newArticle);

        return json_encode($original);
    }

    /**
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
     * @Route('/preview')
     */
    public function preview($content)
    {
        return (new Template($content))->render();
    }
}
