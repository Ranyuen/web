<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Controller;

use Ranyuen\Model\Article;
use Ranyuen\Model\ArticleTag;

/**
 * Admin news
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class AdminNewsController extends AdminController
{
    /**
     * @var Ranyuen\DbCapsule
     * @Inject
     */
    private $db;

    public function make()
    {
        if ($this->auth()) {
            echo $this->renderer->render('admin/news/new', ['tags' => ArticleTag::all()]);
        }
        $this->logger->addAccessInfo();
    }

    public function edit($id)
    {
        if ($this->auth()) {
            $article = Article::with('tags')->find($id);
            if (!$article) {
                $this->router->notFound();
            }
            echo $this->renderer->render('admin/news/edit', ['article' => $article, 'tags' => ArticleTag::all()]);
        }
        $this->logger->addAccessInfo();
    }

    public function create()
    {
        if ($this->auth()) {
            $article = null;
            $hasSaved = true;
            $this->db->transaction(function () use (&$article, &$hasSaved) {
                $article = Article::create($this->router->request->post());
                $article->fill($this->router->request->put());
                $hasSaved = !$article->isDirty() && $hasSaved;
                $hasSaved = $article->syncTagsByTagNames(
                    explode(',', trim($this->router->request->post('tags'), ', '))
                ) && $hasSaved;
            });
            if (!$hasSaved) {
                echo $this->renderer->render('admin/news/new', ['article' => $article, 'tags' => ArticleTag::all()]);
            } else {
                $this->router->response->redirect("/admin/news/edit/$article->id", 303);
            }
        }
        $this->logger->addAccessInfo();
    }

    public function update($id)
    {
        if ($this->auth()) {
            $article = null;
            $hasSaved = true;
            $this->db->transaction(function () use (&$article, &$hasSaved) {
                $article = Article::find($id);
                if (!$article) {
                    $this->router->notFound();
                }
                $article->fill($this->router->request->put());
                $hasSaved = $article->save() && $hasSaved;
                $hasSaved = $article->syncTagsByTagNames(
                    explode(',', trim($this->router->request->post('tags'), ', '))
                ) && $hasSaved;
            });
            if (!$hasSaved) {
                echo $this->renderer->render('admin/news/edit', ['article' => $article]);
            } else {
                $this->router->response->redirect("/admin/news/edit/$article->id", 303);
            }
        }
        $this->logger->addAccessInfo();
    }

    public function destroy($id)
    {
        if ($this->auth()) {
            Article::destroy($id);
            $this->router->response->redirect('/admin/', 303);
        }
        $this->logger->addAccessInfo();
    }
}
