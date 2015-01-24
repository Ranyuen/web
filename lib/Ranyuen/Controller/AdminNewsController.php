<?php
/**
 * Ranyuen web site
 */

namespace Ranyuen\Controller;

use Ranyuen\Little\Response;
use Ranyuen\Model\Article;
use Ranyuen\Model\ArticleTag;

/**
 * Admin news
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @Route('/admin/news')
 */
class AdminNewsController extends AdminController
{
    /**
     * @var Ranyuen\DbCapsule
     * @Inject
     */
    private $db;

    /** @Route('/new') */
    public function make()
    {
        $this->auth();

        return $this->renderer->render('admin/news/new', ['tags' => ArticleTag::all()]);
    }

    /** @Route('/edit/{id}') */
    public function edit($id)
    {
        $this->auth();
        $article = Article::with('tags')->find($id);
        if (!$article) {
            $this->router->notFound();
        }

        return $this->renderer->render('admin/news/edit', ['article' => $article, 'tags' => ArticleTag::all()]);
    }

    /** @Route('/create',via=POST) */
    public function create()
    {
        $this->auth();
        $article = null;
        $hasSaved = true;
        $this->db->transaction(
            function () use (&$article, &$hasSaved) {
                $article = Article::create($this->router->request->post());
                $article->fill($this->router->request->put());
                $hasSaved = !$article->isDirty() && $hasSaved;
                $hasSaved = $article->syncTagsByTagNames(
                    explode(',', trim($this->router->request->post('tags'), ', '))
                ) && $hasSaved;
            }
        );
        if (!$hasSaved) {
            echo $this->renderer->render('admin/news/new', ['article' => $article, 'tags' => ArticleTag::all()]);
        } else {
            $this->router->response->redirect("/admin/news/edit/$article->id", 303);
        }
    }

    /** @Route('/update/{id}',via=PUT) */
    public function update($id)
    {
        $this->auth();
        $article = null;
        $hasSaved = true;
        $this->db->transaction(
            function () use ($id, &$article, &$hasSaved) {
                $article = Article::find($id);
                if (!$article) {
                    $this->router->notFound();
                }
                $article->fill($this->router->request->put());
                $hasSaved = $article->save() && $hasSaved;
                $hasSaved = $article->syncTagsByTagNames(
                    explode(',', trim($this->router->request->post('tags'), ', '))
                ) && $hasSaved;
            }
        );
        if (!$hasSaved) {
            echo $this->renderer->render('admin/news/edit', ['article' => $article]);
        } else {
            $this->router->response->redirect("/admin/news/edit/$article->id", 303);
        }
    }

    /** @Route('/destroy/{id}',via=DELETE) */
    public function destroy($id)
    {
        $this->auth();
        Article::destroy($id);
        $this->router->response->redirect('/admin/', 303);
    }
}
