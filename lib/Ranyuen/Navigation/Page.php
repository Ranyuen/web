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
    static public function fromElement($lang, $parentPath, \SimpleXMLElement $elm)
    {
        $params = [];
        foreach ($elm->attributes() as $key => $val) {
            $params[$key] = $val;
        }
        if (isset($params['path']) && 'index' === (string) $params['path']) {
            $params['path'] = '';
        }
        return new self($lang, $parentPath, $params);
    }

    static public function fromArticle($lang, Article $article)
    {
        $content = $article->getContent($lang);
        return new self($lang, '', [
            'path'       => $article->path,
            'title'      => $content ? $content->plainTitle() : $article->path,
            'article_id' => $article->id,
        ]);
    }

    public $lang;
    public $path;
    public $title;

    private $article_id;

    public function __construct($lang, $parentPath, $params)
    {
        $this->lang       = $lang;
        $this->path       = isset($params['path'])       ? $parentPath.(string) $params['path']   : null;
        $this->title      = isset($params['title'])      ? (string) $params['title']              : null;
        $this->article_id = isset($params['article_id']) ? intval((string) $params['article_id']) : 0;
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
            $this->path = $article->path;
            if (!$this->title) {
                $this->title = $article->getContent($lang)->plainTitle();
            }
        }
    }

    public function __toString()
    {
        return json_encode([
            'lang'  => $this->lang,
            'path'  => $this->path,
            'title' => $this->title,
        ]);
    }
}
