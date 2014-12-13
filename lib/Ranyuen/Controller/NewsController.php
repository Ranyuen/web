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
 */
class NewsController extends Controller
{
    /**
     * @var Ranyuen\Router
     * @Inject
     */
    private $router;
    /** @var Ranyuen\Renderer */
    private $renderer;
    /**
     * @var Ranyuen\Renderer
     * @Inject
     */
    private $articleRenderer;
    /**
     * @var Ranyuen\Logger
     * @Inject
     */
    private $logger;

    public function __construct($renderer)
    {
        $this->renderer = $renderer;
    }

    public function index($lang = null)
    {
        if (!$lang) {
            $lang = $this->config['lang']['default'];
        }
        $tags = ArticleTag::allPrimaryTag();
        if (is_null($tags)) {
            $tags = [];
        }
        $params = array_merge(
            $this->getDefaultParams($lang, 'news/index'),
            ['tags' => $tags]
        );
        echo $this->renderer->render("news/index.$lang", $params);
        $this->logger->addAccessInfo();
    }

    public function lists($lang = null)
    {
        if (!$lang) {
            $lang = $this->config['lang']['default'];
        }
        $articles = [];
        $tags = ArticleTag::findByName($this->router->request->get('tag'));
        if ($tags) {
            $articles = $tags->articles;
        }
        $params = array_merge(
            $this->getDefaultParams($lang, 'news/index'),
            ['articles' => $articles]
        );
        echo $this->renderer->render("news/list.$lang", $params);
        $this->logger->addAccessInfo();
    }

    public function show($url, $lang = null)
    {
        if (!$lang) {
            $lang = $this->config['lang']['default'];
        }
        $article = Article::where(['url' => $url, 'lang' => $lang])->first();
        if (!$article) {
            $this->router->notFound();
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
                'title'   => strip_tags($article->title),
                'article' => $article,
            ]
        );
        echo $this->renderer->render("news/show.$lang", $params);
        $this->logger->addAccessInfo();
    }
}
