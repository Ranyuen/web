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
     * @var Ranyuen\DbCapsule
     * @Inject
     */
    private $db;

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

        return $this->renderer->render('admin/articles/edit', ['article' => json_encode($article)]);
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
        $entity = null;
        $this->db->transaction(function () use ($article, &$entity) {
            if (0 === $article->id) {
                $entity = new Article();
            } else {
                if (!$entity = Article::with('contents')->find($article->id)) {
                    return $router->error(404, $req);
                }
                foreach ($entity->contents as $content) {
                    $content->delete();
                }
            }
            $entity->path = $article->path;
            foreach ($article->contents as $val) {
                $content = new ArticleContent();
                $content->lang       = $val->lang;
                $content->content    = $val->content;
                $content->article_id = $entity->id;
                $entity->contents[]  = $content;
            }
            $entity->push();
        });
        return json_encode($entity);
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

        return new Response('', 303, ['Location' => '/admin']);
    }

    /**
     * @Route('/preview')
     */
    public function preview($content)
    {
        return (new Template($content))->render();
    }
}
