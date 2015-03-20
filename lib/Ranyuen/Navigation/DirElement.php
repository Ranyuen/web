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
        switch ($elm->getName()) {
        case 'lang':
            $this->path = $parentPath;
            break;
        case 'dir':
            $this->path = $parentPath.$elm->attributes()['path'].'/';
            break;
        default:
            throw new \Exception(print_r($elm, true));
        }
    }

    /**
     * Page descendants.
     */
    public function pages()
    {
        $pages = [$this->indexPage()];
        foreach ($this->elm->children() as $elm) {
            switch ($elm->getName()) {
            case 'dir':
                $pages[] = (new DirElement($this->lang, $this->path, $elm))->pages();
                break;
            case 'page':
                if ('index' === (string) $elm->attributes()['path']) {
                    break;
                }
                $pages[] = Page::fromElement($this->lang, $this->path, $elm);
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

    public function childPages()
    {
        $pages = [$this->indexPage()];
        foreach ($this->elm->xpath('page[@path!="index"]') as $elm) {
            $pages[] = Page::fromElement($this->lang, $this->path, $elm);
        }
        return array_values(array_unique($pages, SORT_STRING));
    }

    public function indexPage()
    {
        if ($elm = $this->elm->xpath('page[@path="index"]')) {
            return Page::fromElement($this->lang, $this->path, $elm);
        }
        if ($article = Article::findByPath($this->path)) {
            return Page::fromArticle($this->lang, $article);
        }
        return new Page($this->lang, '', [
            'path'  => $this->path,
            'title' => 'Index',
        ]);
    }
}
