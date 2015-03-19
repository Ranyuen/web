<?php
/**
 * Ranyuen web site.
 */

namespace Ranyuen\Navigation;

use Ranyuen\Model\Article;

/**
 */
class Page
{
    static public function fromElement($lang, \SimpleXMLElement $elm)
    {
        return new self($lang, $elm->attributes());
    }

    static public function fromArticle($lang, Article $article)
    {
        return new self($lang, [
            'path'  => $article->path,
            'title' => $article->getContent($lang)->title,
        ]);
    }

    public $lang;
    public $path;
    public $title;

    private $article_id;

    public function __construct($lang, $params)
    {
        $this->lang       = $lang;
        $this->path       = (string) $params['path'];
        $this->title      = (string) $params['title'];
        $this->article_id = intval((string) $params['article_id']);
        if (!($this->path && $this->title)) {
            if ($this->path) {
                $article = Article::with('contents')->where('path', $this->path)->first();
            } elseif ($this->article_id) {
                $article = Article::with('contents')->find($this->article_id);
            }
            if (!$article) {
                // TODO 本来はエラーです。dev環境ではDBに記事が無いので、だうするか考へませう。
                $this->title = $this->path;
                return $this;
                //throw new \Exception(print_r($this, true));
            }
            $this->path  = $article->path;
            $this->title = $this->title || $article->getContent($lang)->title;
        }
    }
}
