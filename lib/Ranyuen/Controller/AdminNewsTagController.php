<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Controller;

use Ranyuen\Model\ArticleTag;

/**
 * Admin news_tag
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class AdminNewsTagController extends AdminController
{
    public function make()
    {
        if ($this->auth()) {
            echo $this->renderer->render('admin/news_tag/new');
        }
        $this->logger->addAccessInfo();
    }

    public function edit($id)
    {
        if ($this->auth()) {
            $tag = ArticleTag::find($id);
            if (!$tag) {
                $this->router->notFound();
            }
            echo $this->renderer->render('/admin/news_tag/edit', ['tag' => $tag]);
        }
        $this->logger->addAccessInfo();
    }

    public function create()
    {
        if ($this->auth()) {
            $tag = ArticleTag::create($this->router->request->post());
            if ($tag->isDirty()) {
                echo $this->renderer->render('admin/news_tag/new', ['tag' => $tag]);
            } else {
                $this->router->response->redirect("/admin/news_tag/edit/$tag->id", 303);
            }
        }
        $this->logger->addAccessInfo();
    }

    public function update($id)
    {
        if ($this->auth()) {
            $tag = ArticleTag::find($id);
            if (!$tag) {
                $this->router->notFound();
            }
            $tag->fill($this->router->request->put());
            if (!$tag->save()) {
                echo $this->renderer->render('admin/news/edit', ['tag' => $tag]);
            } else {
                $this->router->response->redirect("/admin/news_tag/edit/$tag->id", 303);
            }
        }
        $this->logger->addAccessInfo();
    }

    public function destroy($id)
    {
        if ($this->auth()) {
            ArticleTag::destroy($id);
            $this->router->response->redirect('/admin/', 303);
        }
        $this->logger->addAccessInfo();
    }
}
