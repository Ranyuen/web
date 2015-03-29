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
 * Nav element latest-pages.
 */
class LatestPagesElement
{
    private $dir;
    private $elm;
    private $count;

    public function __construct(DirElement $dir, \SimpleXMLElement $elm)
    {
        $this->dir   = $dir;
        $this->elm   = $elm;
        $this->count = intval($elm->attributes()['count']);
    }

    /**
     * Latest pages.
     *
     * @return Page[]
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function pages()
    {
        $existingPaths = array_map(
            function ($page) {
                return $page->path;
            },
            $this->dir->childPages()
        );
        $articles = Article::children($this->dir->path, $this->count);
        $pages = [];
        foreach ($articles as $article) {
            if (in_array($article->path, $existingPaths)
                || !$article->getContent($this->dir->lang)
            ) {
                continue;
            }
            $pages[] = Page::fromArticle($this->dir->lang, $article);
        }

        return $pages;
    }
}
