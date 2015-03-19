<?php
/**
 * Ranyuen web site.
 */

namespace Ranyuen\Navigation;

use Ranyuen\Model\Article;

/**
 */
class DirElement
{
    public $lang;
    public $path;

    private $elm;

    public function __construct($lang, $parentPath, \SimpleXMLElement $elm)
    {
        $this->lang = $lang;
        $this->elm  = $elm;
        $this->path = $parentPath.$elm->attributes()['path'].'/';
    }

    public function pageChildren()
    {
        $pages = [];
        if ($elm = $this->elm->xpath('page[@path="index"]')) {
            $pages[] = Page::fromElement($this->lang, $elm);
        } if ($article = Article::findByPath($this->path)) {
            $pages[] = Page::fromArticle($this->lang, $article);
        }
        foreach ($this->elm->xpath('page') as $elm) {
            $page = Page::fromElement($this->lang, $elm);
            if (preg_match('/\/\z/', $page->path)) {
                continue;
            }
            $pages[] = $page;
        }
        return $pages;
    }

    /**
     * Page descendants.
     */
    public function pages()
    {
        $pages = [];
        foreach ($this->elm->children() as $elm) {
            switch ($elm->getName()) {
            case 'dir':
                $pages[] = (new DirElement($this->lang, $this->path, $elm))->pages();
                break;
            case 'page':
                $pages[] = Page::fromElement($this->lang, $elm);
                break;
            case 'rest-pages':
                $pages = array_merge($pages, (new RestPagesElement($this, $elm))->pages());
                break;
            case 'latest-pages':
                $pages = array_merge($pages, (new LatestPagesElement($this, $elm))->pages());
                break;
            default:
                throw new \Exception(print_r($elm, true));
            }
        }
        return $pages;
    }
}
