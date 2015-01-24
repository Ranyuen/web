<?php
/**
 * Ranyuen web site
 */

namespace Ranyuen\Controller;

use Ranyuen\Little\Response;
use Ranyuen\Model\ArticleTag;

/**
 * Admin news_tag
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @Route('/admin/news_tag')
 */
class AdminNewsTagController extends AdminController
{
    /**
     * @return string
     *
     * @Route('/new')
     */
    public function make()
    {
        $this->auth();

        return $this->renderer->render('admin/news_tag/new');
    }

    /**
     * @param string $id News tag ID.
     *
     * @return string|Response
     *
     * @Route('/edit/{id}')
     */
    public function edit($id)
    {
        $this->auth();
        if (!($tag = ArticleTag::find($id))) {
            return new Response('', 404);
        }

        return $this->renderer->render('/admin/news_tag/edit', ['tag' => $tag]);
    }

    /**
     * @return string|Response
     *
     * @Route('/create',via=POST)
     */
    public function create()
    {
        $this->auth();
        $tag = ArticleTag::create($this->router->request->post());
        if ($tag->isDirty()) {
            return $this->renderer->render('admin/news_tag/new', ['tag' => $tag]);
        }

        return new Response('', 303, ['Location' => "/admin/news_tag/edit/$tag->id"]);
    }

    /**
     * @param Request $req HTTP request.
     * @param string  $id  News tag ID.
     *
     * @return string|Response
     *
     * @Route('/update/{id}',via=PUT)
     */
    public function update(Request $req, $id)
    {
        $this->auth();
        if (!($tag = ArticleTag::find($id))) {
            return new Response('', 404);
        }
        $tag->fill($req->request);
        if (!$tag->save()) {
            return $this->renderer->render('admin/news/edit', ['tag' => $tag]);
        }

        return new Response('', 303, ['Location' => "/admin/news_tag/edit/$tag->id"]);
    }

    /**
     * @param string $id News tag ID.
     *
     * @return Response
     *
     * @Route('/destroy/{id}',via=DELETE)
     */
    public function destroy($id)
    {
        $this->auth();
        ArticleTag::destroy($id);

        return new Response('', 303, ['Location' => '/admin/']);
    }
}
