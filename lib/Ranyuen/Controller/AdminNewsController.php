<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Controller;

use Ranyuen\Model\Article;

/**
 * Admin news
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class AdminNewsController extends AdminController
{
    public function newNews()
    {
        $this->auth();
        $this->renderer->setLayout('admin/layout');
        echo $this->renderer->render('admin/news/new');
        $this->logger->addAccessInfo();
    }

    public function edit($id)
    {
        $this->auth();
        $article = Article::find($id);
        if (!$article) {
            $this->router->notFound();
        }
        $this->renderer->setLayout('admin/layout');
        echo $this->renderer->render('admin/news/edit', ['article' => $article]);
        $this->logger->addAccessInfo();
    }

    public function create()
    {
        $this->auth();
        $article = Article::create($this->router->request->post());
        if ($article->isDirty()) {
            $this->renderer->setLayout('admin/layout');
            echo $this->renderer->render('admin/news/new', ['article' => $article]);
        } else {
            $this->router->response->redirect("/admin/news/edit/$article->id", 303);
        }
        $this->logger->addAccessInfo();
    }

    public function update($id)
    {
        $this->auth();
        $article = Article::find($id);
        if (!$article) {
            $this->router->notFound();
        }
        $article->fill($this->router->request->post());
        if (!$article->save()) {
            $this->renderer->setLayout('admin/layout');
            echo $this->renderer->render('admin/news/edit', ['article' => $article]);
        } else {
            $this->router->response->redirect("/admin/news/edit/$article->id", 303);
        }
        $this->logger->addAccessInfo();
    }

    public function destroy($id)
    {
        $this->auth();
        Article::destroy($id);
        $this->router->response->redirect("/admin/", 303);
        $this->logger->addAccessInfo();
    }
}
