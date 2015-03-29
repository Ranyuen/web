<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Navigation;

use Ranyuen\Model\Article;

/**
 * Nav element page.
 */
class Page
{
    /**
     * Construct ftom SimpleXMLElement.
     *
     * @param string           $lang       Lang. ja or en.
     * @param string           $parentPath Current dir.
     * @param SimpleXMLElement $elm        Page element.
     *
     * @return self
     */
    public static function fromElement($lang, $parentPath, \SimpleXMLElement $elm)
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

    /**
     * Construct from Article model.
     *
     * @param string  $lang    Lang. ja or en.
     * @param Article $article Article model.
     *
     * @return self
     */
    public static function fromArticle($lang, Article $article)
    {
        $content = $article->getContent($lang);

        return new self(
            $lang,
            '',
            [
                'path'       => $article->path,
                'title'      => $content ? $content->plainTitle() : $article->path,
                'article_id' => $article->id,
            ]
        );
    }

    public $lang;
    public $path;
    public $title;

    private $articleId;

    /**
     * Constructor.
     *
     * @param string $lang       Lang. ja.or en.
     * @param string $parentPath Current dir.
     * @param array  $params     Page parameters.
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct($lang, $parentPath, $params)
    {
        $this->lang      = $lang;
        $this->path      = isset($params['path'])       ? $parentPath.(string) $params['path']   : null;
        $this->title     = isset($params['title'])      ? (string) $params['title']              : null;
        $this->articleId = isset($params['article_id']) ? intval((string) $params['article_id']) : 0;
        $this->loadParamsFromDb();
    }

    public function __toString()
    {
        return json_encode(
            [
            'lang'  => $this->lang,
            'path'  => $this->path,
            'title' => $this->title,
            ]
        );
    }

    private function loadParamsFromDb()
    {
        if ($this->path && $this->title) {
            return;
        }
        if ($this->path) {
            $article = Article::with('contents')->where('path', $this->path)->first();
        } elseif ($this->articleId) {
            $article = Article::with('contents')->find($this->articleId);
        }
        if (!$article) {
            // 本来はエラーです。dev環境ではDBに記事が無いので、だうするか考へませう。
            $this->title = $this->path;

            return $this;
            //throw new \Exception(print_r($this, true));
        }
        $this->path = $article->path;
        if (!$this->title) {
            $this->title = $article->getContent($this->lang)->plainTitle();
        }
    }
}
