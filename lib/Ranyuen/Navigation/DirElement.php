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
 * Nav element dir.
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
     *
     * @return Page[]
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
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
                    $attrs = $elm->attributes();
                    if (isset($attrs['path']) && 'index' === (string) $attrs['path']) {
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

    /**
     * Chils page elements.
     *
     * @return Page[]
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function childPages()
    {
        $pages = [$this->indexPage()];
        foreach ($this->elm->xpath('page[@path!="index"]') as $elm) {
            $pages[] = Page::fromElement($this->lang, $this->path, $elm);
        }

        return array_values(array_unique($pages, SORT_STRING));
    }

    /**
     * Index page.
     *
     * @return Page
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function indexPage()
    {
        if ($elms = $this->elm->xpath('page[@path="index"]')) {
            return Page::fromElement($this->lang, $this->path, $elms[0]);
        }
        if ($article = Article::findByPath($this->path)) {
            return Page::fromArticle($this->lang, $article);
        }

        return new Page(
            $this->lang,
            '',
            [
            'path'  => $this->path,
            'title' => 'Index',
            ]
        );
    }
}
