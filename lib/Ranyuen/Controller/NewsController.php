<?php
/**
 * Ranyuen web site
 */

namespace Ranyuen\Controller;

use Ranyuen\Model\Article;
use Ranyuen\Model\ArticleTag;
use Ranyuen\Renderer;

/**
 * News
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @Route('/news')
 */
class NewsController extends Controller
{
    /** @var
     * Ranyuen\Renderer
     * @Inject
     */
    private $renderer;
    /**
     * @var Ranyuen\Renderer
     * @Inject
     */
    private $articleRenderer;

    /**
     * @param string $lang Request lang.
     *
     * @return string
     *
     * @Route('/')
     */
    public function index($lang)
    {
        $tags = ArticleTag::allPrimaryTag();
        if (is_null($tags)) {
            $tags = [];
        }
        $params = array_merge(
            $this->getDefaultParams($lang, 'news/index'),
            ['tags' => $tags]
        );

        return $this->renderer->render("news/index.$lang", $params);
    }

    /**
     * @param string $lang Request lang.
     *
     * @return string
     *
     * @Route('/list')
     */
    public function lists($lang)
    {
        $articles = [];
        $tags = ArticleTag::findByName($this->router->request->get('tag'));
        if ($tags) {
            $articles = $tags->articles;
        }
        $params = array_merge(
            $this->getDefaultParams($lang, 'news/index'),
            ['articles' => $articles]
        );

        return $this->renderer->render("news/list.$lang", $params);
    }

    /**
     * @param string $url  New url.
     * @param string $lang Request lang.
     *
     * @return string
     *
     * @Route('/{url}')
     */
    public function show($url, $lang)
    {
        $article = Article::where(['url' => $url, 'lang' => $lang])->first();
        if (!$article) {
            return new Response('', 404);
        }
        $this->articleRenderer->setLayout(null);
        $article->content = $this->articleRenderer->renderTemplate(
            $article->content,
            [
                'title'       => $article->title,
                'description' => $article->description,
            ]
        );
        $params = array_merge(
            $this->getDefaultParams($lang, "news/$url"),
            [
                'title'   => $article->plainTitle(),
                'article' => $article,
            ]
        );

        return $this->renderer->render("news/show.$lang", $params);
    }
}
