<?php
/**
 * Ranyuen web site.
 */

namespace Ranyuen\Navigation;

use Ranyuen\Model\Article;

/**
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
        $this->count = $elm->attributes['count'] || null;
    }

    public function pages()
    {
        return array_map(
            function ($article) {
                return Page::fromArticle($this->dir->lang, $article);
            },
            Article::children($this->dir->path, $this->count)
        );
    }
}
