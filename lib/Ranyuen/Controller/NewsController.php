<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Controller;

use Ranyuen\Model\Article;
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
        $articles = Article::where('lang', $lang)
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->get();
        if (is_null($articles)) {
            $articles = [];
        }
        $params = array_merge(
            $this->getDefaultParams($lang, 'news/index'),
            ['articles' => $articles]
        );
        echo $this->renderer->render("news/index.$lang", $params);
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
        $article->content = (new Renderer(''))->renderTemplate(
            $article->content,
            [
                'title'       => $article->title,
                'description' => $article->description,
            ]
        );
        $params = array_merge(
            $this->getDefaultParams($lang, "news/$url"),
            ['article' => $article]
        );
        echo $this->renderer->render("news/show.$lang", $params);
        $this->logger->addAccessInfo();
    }
}
