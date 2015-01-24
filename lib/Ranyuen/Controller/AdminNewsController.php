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

    /**
     * @return string
     *
     * @Route('/new')
     */
    public function make()
    {
        $this->auth();

        return $this->renderer->render('admin/news/new', ['tags' => ArticleTag::all()]);
    }

    /**
     * @param string $id News ID.
     *
     * @return string
     *
     * @Route('/edit/{id}')
     */
    public function edit($id)
    {
        $this->auth();
        $article = Article::with('tags')->find($id);
        if (!$article) {
            $this->router->notFound();
        }

        return $this->renderer->render('admin/news/edit', ['article' => $article, 'tags' => ArticleTag::all()]);
    }

    /**
     * @return string|Response
     *
     * @Route('/create',via=POST)
     */
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
            return $this->renderer->render('admin/news/new', ['article' => $article, 'tags' => ArticleTag::all()]);
        }

        return new Response('', 303, ['Location' => "/admin/news/edit/$article->id"]);
    }

    /**
     * @param string $id News ID.
     *
     * @return string|Response
     *
     * @Route('/update/{id}',via=PUT)
     */
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
            return $this->renderer->render('admin/news/edit', ['article' => $article]);
        }

        return new Response('', 303, ['Location' => "/admin/news/edit/$article->id"]);
    }

    /**
     * @param string $id News ID.
     *
     * @return Response
     *
     * @Route('/destroy/{id}',via=DELETE)
     */
    public function destroy($id)
    {
        $this->auth();
        Article::destroy($id);

        return new Response('', 303, ['Location' => '/admin']);
    }
}
